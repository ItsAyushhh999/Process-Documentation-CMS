<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request\Project\ProjectCreateRequest;
use App\Http\Requests\Request\Project\ProjectUpdateRequest;
use App\Http\Services\PermissionService;
use App\Models\Document;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectDeploymentMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except('index');
    }

    public function index(Project $project, Document $document)
    {
        // $documentId = Document::get('id')->first();
        $projects = Project::withCount('documents')->where('sub_Projects', '0')->get();

        // return view('project.index', ['projects'=>$projects, 'documentId' => $documentId]);
        return Inertia::render('Projects/index', [
            'projects'=>$projects,
            // 'documentId' => $documentId
        ]);
    }

    public function create()
    {
        return view('project.create');
    }

    public function store(ProjectCreateRequest $request)
    {
        $path = storage_path('/app/public/projects/' . $request->name);

        Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url ?: '',
            'development_pipeline' => $request->development_pipeline,
            'staging_pipeline' => $request->staging_pipeline,
            'production_Pipeline' => $request->production_Pipeline,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        //create project directory
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        Session::flash('success', 'Poject added Successfully.');

        return redirect(route('projects.index'));
    }

    public function edit(Project $project)
    {
        return view('project.create', ['project'=>$project]);
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {

        // Validate unique stage for each project
        $this->validate($request, [
            'deployment_mappings.*.stage' => "required|distinct|unique:project_deployment_mappings,stage,$project->id,project_id,project_id," . $project->id,
            'deployment_mappings.*.account_identifier' => 'required',
            'deployment_mappings.*.role_session_name' => 'required',
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Check if deployment_mappings is empty
            if (empty($request->deployment_mappings)) {
                // Delete all deployment mappings for the project
                ProjectDeploymentMapping::where('project_id', $project->id)->delete();
            } else {
                $idsToDelete = array_diff(
                    ProjectDeploymentMapping::where('project_id', $project->id)->pluck('id')->toArray(),
                    collect($request->deployment_mappings)->pluck('id')->filter()->toArray()
                );

                if ($idsToDelete) {
                    ProjectDeploymentMapping::whereIn('id', $idsToDelete)->delete();
                }

                // Process and update existing or new mappings
                foreach ($request->deployment_mappings as $mapping) {
                    // Update existing mapping
                    $data = [
                        'project_id'         => $project->id,
                        'stage'              => $mapping['stage'],
                        'account_identifier' => (string) $mapping['account_identifier'],
                        'role_session_name'  => $mapping['role_session_name'],
                        'updated_by'         => Auth::user()->id,
                    ];

                    if (empty($mapping['id'])) {
                        $data['created_by'] = Auth::user()->id;  // Add created_by only if it's a new record
                    }

                    // Update or create mapping
                    ProjectDeploymentMapping::updateOrCreate(
                        ['id' => $mapping['id'] ?? null],
                        $data
                    );
                }
            }

            // Update the project details
            $project->update([
                'name'                  => $request->name,
                'description'           => $request->description,
                'url'                   => $request->url ?? '',
                'repository_name'       => $request->repository_name,
                'development_pipeline'  => $request->development_pipeline,
                'staging_pipeline'      => $request->staging_pipeline,
                'production_Pipeline'   => $request->production_Pipeline,
                'updated_by'            => Auth::user()->id,
            ]);

            // Commit transaction
            DB::commit();

            return back()->with('success', 'Project Updated Successfully');
        } catch (\Exception $e) {
            // Rollback transaction if error occurs
            DB::rollback();

            return back()->with('error', 'An error occurred while updating the project. Please try again.');
        }
    }

    public function viewSubProjects($id)
    {

        $sub_projects = Project::with(['createdBy', 'updatedBy', 'deploymentMappings'])
        ->where('sub_projects', $id)
        ->get();

        // $user = User::where('id',)
        // return view('project.subproject', [
        //     'sub_projects' => $sub_projects,
        // ]);

        return Inertia::render('Projects/SubProject', [
            'sub_projects' => $sub_projects,
            'project_id' => $id,
        ]);
    }

    public function subProject(Request $request, PermissionService $permissionService)
    {
        $this->validate($request, [
            'name' => 'required',
            'url' => 'nullable|url',
            'repository' =>'nullable',
            'development_pipeline' => 'nullable',
            'staging_pipeline' => 'nullable',
            'production_Pipeline' => 'nullable',
            'description' => 'nullable',
            'project_id_for_sub_project' => 'required',
        ]);

        //Displaying error when validation fails
        // if ($validation->fails()) {
        //     $errors = $validation->errors();
        //     $errorMsg = $errors->first();

        //     return back()->with('error', $errorMsg);
        // }

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url ?: '',
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'repository_name' => $request->repository,
            'sub_projects' => $request->project_id_for_sub_project,
            'development_pipeline' => $request->development_pipeline,
            'staging_pipeline' => $request->staging_pipeline,
            'production_Pipeline' => $request->production_Pipeline,
        ]);

        return back()->with('success', 'Sub project created successfully');
    }

    public function sortCategory(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'projectId' => 'required|exists:projects,id',
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->with('error', $validation->errors()->first());
        }

        $projectId = $request->get('projectId');
        $ids = $request->get('ids');

        $caseStatements = [];

        foreach ($ids as $index => $categoryId) {
            $caseStatements[] = "WHEN category_id = $categoryId THEN " . ($index + 1);
        }

        $caseSQL = implode(' ', $caseStatements);

        ProjectCategory::query()
        ->where('project_id', $projectId)
        ->whereIn('category_id', $ids)
        ->update(['position' => DB::raw("(CASE $caseSQL END)")]);

        return redirect()->back()->with('success', 'Category order updated successfully.');
    }
}

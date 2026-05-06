<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request\Document\DocumentCreateRequest;
use App\Http\Traits\DepartmentCheck;
use App\Models\Category;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class DocumentController extends Controller
{
    use DepartmentCheck;

    public function __construct()
    {
        // $this->middleware('admin')->except('index', 'show');
    }

    public function index($id = NULL)
    {
        $isPermitted = $this->CheckPermission(FacadesAuth::user()->id);

        $project = Project::find($id);
        if (!$project) {
            return redirect(route('projects.index'))->with('error', 'Project not found.');
        }

        $document = Document::where('project_id', $id)->first();

        if (!$document) {
            // dd($document);
            // return view(
            //     'documents.show',
            //     [
            //         'document'    => $document,
            //         'categories'  => null,
            //         'project_id'  => $id,
            //         'isPermitted' => $isPermitted,
            //     ]
            // );
            return Inertia::render('Documents/index', [
                    'document'    => $document,
                    'categories'  => null,
                    'project_id'  => $id,
                    'isPermitted' => $isPermitted,
            ]);
        }

        $description = $project->description;

        $categories = Category::with(['documents' => function ($query) {
            $query->select('documents.id', 'name');
        }])
            ->whereHas('documents', function ($query) use ($id) {
                $query->where('project_id', $id);
            })->get();

        $isPermitted = $this->CheckPermission(FacadesAuth::user()->id);

        // dd($isPermitted);
        // return view('documents.display', ['document' => $document, 'categories' => $categories, 'project_id' => $id, 'projects' => $projects, 'isPermitted' => $isPermitted, 'description' => $description]);

        return redirect(route('documents.show', [$id, $document->id]));
        // return Inertia::render('Documents/index', [$id, $document->id]);
    }

    public function projectDocuments($projectId, $document = null)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return redirect(route('projects.index'))->with('error', 'Project not found.');
        }

        $projects = Project::select('id', 'name as group_name', 'name', 'sub_projects')->where('sub_projects', '0')->with('subprojects')->get();
        $document = Document::find($document ?: $project?->documents?->first()?->id);

        // $categories = Category::with(['documents' => function ($query) use ($projectId) {
        //     $query->select('documents.id', 'name')->where('project_id', $projectId)->orderBy('position', 'ASC');
        // }])
        //     ->whereHas('documents', function ($query) use ($projectId) {
        //         $query->where('project_id', $projectId);
        //     })->get();
        $categories = $project->category()->with(['documents' => function ($query) use ($projectId) {
            $query->select('documents.id', 'name')->where('project_id', $projectId)->orderBy('position', 'ASC');
        }])->whereHas('documents')->get();

        $isPermitted = $this->CheckPermission(FacadesAuth::user()->id);

        // return view('Documents.show', ['document' => $document, 'categories' => $categories, 'project_id' => $projectId, 'projects' => $projects, 'isPermitted' => $isPermitted]);

        return Inertia::render('Documents/index', [
            'document' => $document,
            'categories' => $categories,
            'project_id' => $projectId,
            'projects' => $projects,
            'isPermitted' => $isPermitted,
            'project_name' => $project->name,
            'project_url' => $project->url,
        ]);
    }

    public function create($id)
    {
        // dd($id);
        $project = Project::find($id);
        if (!$project) {
            return back();
        }

        $categories = $project->category()->where('status', '1')->get();

        $isPermitted = $this->CheckPermission(FacadesAuth::user()->id);
        if (!$isPermitted) {
            return back()->with('error', 'Not enough privileges.');
        }

        // return view('documents.create', ['categories' => $categories, 'project' => $project, 'isPermitted' => $isPermitted]);

        return Inertia::render('Documents/create', ['categories' => $categories, 'project' => $project, 'isPermitted' => $isPermitted]);
    }

    public function store(DocumentCreateRequest $request)
    {
        $user = FacadesAuth::user();
        $isPermitted = $this->CheckPermission(FacadesAuth::user()->id);
        if (!$isPermitted) {
            return back()->with('error', 'Not enough privileges.');
        }

        $project = $request->projectId;

        $documents = Document::create([
            'name'        => $request->name,
            'project_id'  => $request->projectId,
            'description' => $request->description,
            'createdBy'   => $user->id,
            'updatedBy'   => $user->id,
        ]);

        $documents->position = $documents->max('position') + 1;
        $documents->save();
        $documents->category()->attach($request->categories);

        // return redirect()->route('documents.index', ['project' => $project]);
        return back()->with('success', 'Document  Created.');
    }

    // public function show($id, Document $document)
    // {
    //     $categories = Category::with(['documents' => function ($query) {
    //         $query->select('documents.id', 'name');
    //     }])
    //         ->whereHas('documents', function ($query) use ($id) {
    //             $query->where('project_id', $id);
    //         })->get();
    //     return view('documents.show', ['document' => $document, 'categories' => $categories, 'project_id' => $id]);
    // }

    public function edit(Document $document)
    {
        $isPermitted = $this->CheckPermission(FacadesAuth::user()->id);
        if (!$isPermitted) {
            return back()->with('error', 'Not enough privileges.');
        }

        // return view(
        //     'documents.edit',
        //     [
        //         'document'         => $document,
        //         'isPermitted'      => $isPermitted,
        //         'categories'       => Category::all(),
        //         'documentCategory' => $document->category->pluck('id')->toArray(),
        //     ]
        // );

        $categories = optional(Project::find($document->project_id))
                    ->category()
                    ->where('status', '1')
                    ->get() ?? back();

        return Inertia::render(
            'Documents/edit',
            [
               'document'         => $document,
               'isPermitted'      => $isPermitted,
               'categories'       => $categories,
               'documentCategory' => $document->category->pluck('id')->toArray(),
            ]
        );
    }

    public function update(DocumentCreateRequest $request, Document $document)
    {
        $user = FacadesAuth::user();
        $isPermitted = $this->CheckPermission(FacadesAuth::user()->id);
        if (!$isPermitted) {
            return back()->with('error', 'Not enough privileges.');
        }

        $project = $document->project_id;
        $document->update([
            'name' => $request->name,
            'isPublished' => $request->get('isPublish'),
            'description' => $request->description,
            'updatedBy'   => $user->id,
        ]);

        $document->category()->sync($request->categories);

        // return redirect()->route('documents.show', ['project' => $project, 'document' => $document]);
        return back()->with('success', 'Document Updated.');
    }

    public function destroy(Document $document, $id)
    {
        Document::find($id)->delete();

        return redirect()->back();
    }

    /**
     * Function for updating status of the documents.
     */
    public function updateStatus(Request $request)
    {

        $document = Document::find($request->get('documentId'));
        if (!$document) {
            return $this->failure('Document not found.', 400);
        }
        //1 => Published & 0 => Unpublished
        $document->isPublished = $document->isPublished === 1 ? 0 : 1;
        $document->updatedBy = FacadesAuth::user()->id;
        $document->save();

        return redirect()->back()->with('success', 'Document ' . ($document->isPublished == 1 ? 'Published.' : 'Unpublished.'));
    }

    /**
     * Changing the list order of the document with drag and drop option.
     */
    public function sortDocument(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'projectId' => 'required|exists:projects,id',
            'ids' => 'required|array',
            'ids.*' => 'exists:documents,id',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->with('error', $validation->errors()->first());
        }

        $projectId = $request->get('projectId');
        $ids = $request->get('ids');

        $caseStatements = [];

        foreach ($ids as $index => $documentId) {
            $caseStatements[] = "WHEN id = $documentId THEN " . ($index + 1);
        }

        $caseSQL = implode(' ', $caseStatements);

        Document::query()
        ->where('project_id', $projectId)
        ->whereIn('id', $ids)
        ->update(['position' => DB::raw("(CASE $caseSQL END)")]);

        return redirect()->back()->with('success', 'Document order updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\Project;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    use ApiTrait;

    public function projectList()
    {
        $projects = Project::select('id', 'name', 'description', 'url')->get();

        if ($projects->isEmpty()) {
            return $this->failure('No project found');
        }

        return $this->success('Projects fetched successfully', $projects);
    }

    // public function createProject(Request $request)
    // {
    //     $validator = Validator::make($request, [
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'url' => 'nullable|url',
    //     ]);
    //     return "success";

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     //create project
    //     $project = Project::create([
    //         'name' => $request->name,
    //         'description' => $request->description,
    //         'url' => $request->url,
    //         // 'created_by' => Auth::user()->id
    //     ]);

    //     // Session::flash('success', 'Project added successfully.');
    //     return response()->json(['project' => $project], 200);
    // }
}

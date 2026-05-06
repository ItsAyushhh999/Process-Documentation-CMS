<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except(['index']);
    }

    public function index()
    {
        // $task_types = TaskType::all();
        $task_types = TaskType::with(['createdBy', 'updatedBy'])->get();

        // return view('task.type.index', ['task_types'=>$task_types]);
        // dd($task_types);
        return Inertia::render('TaskTypes/index', [
            'task_types' => $task_types,
        ]);
    }

    public function create()
    {
        return view('task.type.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_type'=>'required',
        ]);
        $task_type = TaskType::create([
                        'type'=>$request->task_type,
                        'created_by'=>Auth::user()->id,
                    ]);
        if ($task_type) {
            return redirect(route('taskTypes.index'))->with('success', 'Task Type created successfully.');
        }

        return redirect()->back()->with('error', 'Something went wrong.');
    }

    public function edit(TaskType $taskType)
    {
        return view('task.type.edit', ['task_type'=>$taskType]);
    }

    public function update(Request $request, TaskType $taskType)
    {
        $request->validate([
            'task_type'=>'required',
        ]);
        $taskType->update([
            'type'=>$request->task_type,
            'updated_by'=>Auth::user()->id,
        ]);

        return redirect(route('taskTypes.index'))->with('success', 'Task Type updated successfully.');
    }
}

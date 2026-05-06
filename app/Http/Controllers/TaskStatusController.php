<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStatusRequest;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except(['index', 'sortRowOrder']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status', '1');
        $taskStatuses = TaskStatus::with(['created_by:id,name', 'updated_by:id,name'])
            ->where('status', $status)
            ->orderBy('listOrder', 'asc')
            ->get();

        $finalStatusValue = TaskStatus::max('value') + 1;

        // dd($finalStatusValue);

        // return view('task.status.index', ['taskStatuses' => $taskStatuses, 'status' => $status]);
        return Inertia::render('TaskStatuses/index', [
            'taskStatuses' => $taskStatuses, 'status' => $status, 'finalValue' => $finalStatusValue,
        ]);
    }

    public function create()
    {
        return view('task.status.create');
    }

    public function store(TaskStatusRequest $request)
    {
        $status = TaskStatus::create([
            'name'      => $request->name,
            'value'     => $request->value,
            'status'    => $request->status,
            'createdBy' => Auth::user()->id,
            'updatedBy' => Auth::user()->id,
        ]);
        if ($status) {
            return redirect(route('taskStatuses.index'))->with('success', 'Task status created successfully.');
        }

        return redirect()->back()->with('error', 'Something went wrong.');
    }

    public function edit(TaskStatus $taskStatus)
    {
        return view('task.status.create', ['status' => $taskStatus]);
    }

    public function update(TaskStatusRequest $request, TaskStatus $taskStatus)
    {
        $taskStatus->update([
            'name'      => $request->name,
            'value'     => $request->value,
            'status'    => $request->status,
            'updatedBy' => Auth::user()->id,
        ]);

        // return redirect()->route('taskStatuses.index')->with('success', 'Task status updated successfully.');
        return back()->with('success', 'Task status updated successfully.');
    }

    public function sortRowOrder(Request $request)
    {
        // dd($request);
        // Retrieve the new order of IDs from the request
        $newOrder = $request->input('order');

        // Update the order in the database
        foreach ($newOrder as $index => $name) {
            TaskStatus::where('name', $name)->update(['listOrder' => $index + 1]); // Update 'order_column' as per your model
        }

        // return response()->json(['success' => 'Row order updated successfully'], 200);
        return back()->with('success', 'Task status updated successfully.');
    }
}

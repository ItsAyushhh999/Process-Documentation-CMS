<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskWatchList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskWatchListController extends Controller
{
    /**
     * Displaying all the watchlist task.
     */
    public function index()
    {
        $routeFlag = false; // true when draft list

        return response()->json([
            'listTaskWatchLists' => TaskWatchList::with('task')->where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get(),
            'routeFlag'          => $routeFlag,
        ]);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->only('task_id'), [
            'task_id' => 'required|exists:tasks,id',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->with('error', $validation->errors()->first());
        }

        $watchListExists = TaskWatchList::WatchList($request['task_id'], Auth::user()->id)->exists();

        if ($watchListExists) {
            $removeTaskWatchList = TaskWatchList::WatchList($request['task_id'], Auth::user()->id)->first();
            $removeTaskWatchList->delete();

            return redirect()->back()->with('success', 'Task has been removed from watchlist');
        }

        TaskWatchList::create([
            'task_id' => $request['task_id'],
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->back()->with('success', 'Task successfully added to watchlist.');
    }

    public function destroy(Request $request)
    {
        $watchlistTask = TaskWatchList::WatchList($request['remove_task_id'], Auth::user()->id)->first();
        if (!$watchlistTask) {
            return redirect()->back()->with('error', 'Task not found.');
        }
        $watchlistTask->delete();

        return redirect()->back()->with('success', 'Task successfully removed.');
    }
}

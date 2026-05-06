<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search'=>'required',
            'type'=>'required',
        ]);

        if ($request->type == 'projects') {
            $results = Task::with('project:id,name')
                ->where('tasks.id', $request->search)
                ->orWhere('tasks.title', 'like', '%' . $request->search . '%')
                ->orWhere('tasks.description', 'like', '%' . $request->search . '%')
                ->orWhereHas('comments', function ($query) use ($request) {
                    $query->where('comments', 'like', '%' . $request->search . '%');
                })
                ->select('tasks.id', 'tasks.title', 'tasks.description', 'tasks.project_id', 'tasks.status', 'tasks.priority')
                ->distinct()
                ->get();
        } else {  //if ($request->type == 'documents')
            $results = Document::with('project:id,name')
                ->where('id', 'like', '%' . $request->search . '%')
                ->orwhere('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%')
                ->get();
        }

        // return view('search.result', ['results'=>$results, 'type'=>$request->type, 'search'=>$request->search]);

        return Inertia::render('Search/index', [
            'results'=>$results,
            'type'=>$request->type,
            'search'=>$request->search,
        ]);
    }
}

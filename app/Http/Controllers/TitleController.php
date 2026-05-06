<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TitleController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except(['index']);
    }

    public function index()
    {
        return view('title.index', ['titles'=>Title::with(['createdBy', 'updatedBy'])->get()]);

        $titles = Title::with(['createdBy', 'updatedBy'])->get();

        //dd($titles);
        return Inertia::render('Titles/index', [
            'titles' => $titles,
        ]);
    }

    public function create()
    {
        return view('title.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_name'=>'required',
        ]);

        $title = Title::create(array_merge($request->all(), ['created_by'=>Auth::user()->id]));

        // return redirect(route('titles.index'))->with('success', 'Title created successfully.');
        return back()->with('success', 'Title created successfully.');
    }

    public function edit(Title $title)
    {
        return view('title.create', ['title'=>$title]);
    }

    public function update(Request $request, Title $title)
    {
        $request->validate([
            'title_name'=>'required',
        ]);

        $title->update(array_merge($request->all(), ['updated_by'=>Auth::user()->id]));

        return redirect(route('titles.index'))->with('success', 'Title updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectCategory;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except('index');
    }

    public function index(Request $request)
    {
        $getAll = $request->query('showAll');

        // return view('category.index', ['categories'=> Category::with('project')->get()]);
        $projects = Project::all();
        $categories = Category::with(['createdBy', 'updatedBy', 'project'])
                        ->when(!$getAll, function ($query) {
                            $query->where('status', '1');
                        })
                       ->get();

        return Inertia::render('Categories/index', [
            'categories' => $categories,
            'projects' => $projects,
            'getAll'=>$getAll,
        ]);
    }

    public function create()
    {
        $projects = Project::all();

        return view('category.create', ['projects'=>$projects]);
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'name'=>'required',
            'description'=>'required',
            'projects'=>'required',
            'logo'=> 'sometimes|image|max:2048',
            'logo.*'=>'mimes:jpeg,png,jpg,gif,svg',
        ]);
        $category = new Category();

        if ($request->hasFile('logo')) {
            $logo = time() . '_' . '.' . $request->logo->getClientOriginalName();
            $request->logo->storeAs('public/category/logo', $logo);
            $category->logo = $logo;
        }
        $category->name = $request->name;
        $category->description = $request->description;
        $category->created_by = Auth::user()->id;
        $category->status = (string) $request->status;
        $category->save();

        $category->project()->attach($request->projects, ['position' => ProjectCategory::max('position') + 1]);

        // return redirect(route('categories.index'))->with('success', 'Category Added Successfully.');
        return redirect()->back()->with('success', 'Category Added Successfully.');
    }

    public function edit(Category $category)
    {
        return view(
            'category.create',
            ['category'=>$category,
                     'projects'=>Project::all(),
                     'projectCategory'=>ProjectCategory::where('category_id', $category->id)->pluck('project_id'),
                    ]
        );
    }

    public function Update(Request $request, Category $category)
    {
        $request->validate([
            'name'=>'required',
            'description'=>'required',
            'projects'=>'required',
            'logo'=> 'sometimes|image|max:2048',
            'logo.*'=>'mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($request->hasFile('logo')) {
            $logo = time() . '_' . '.' . $request->logo->getClientOriginalName();
            $request->logo->storeAs('public/category/logo', $logo);

            if (\Storage::exists(('public/category/logo/' . $category->logo))) {
                \Storage::delete('public/category/logo/' . $category->logo);
            }
            $category->logo = $logo;
        }

        $category->name = $request->name;
        $category->description = $request->description;
        $category->created_by = Auth::user()->id;
        $category->status = (string) $request->status;
        $category->save();

        $category->project()->sync($request->projects);

        return back()->with('success', 'Category Updated Successfully.');
    }

    public function changeStatus(Category $category)
    {

        $category->update([
            'status'     => $category->status === '1' ? '0' : '1',
            'updated_by' => Auth::user()->id,
        ]);

        return back()->with('success', 'Category status updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except(['index']);
    }

    public function index()
    {
        // return view('department.index', ['departments'=>Department::with(['createdBy', 'updatedBy'])->get()]);
        $departments = Department::with(['createdBy', 'updatedBy'])->get();

        return Inertia::render('Departments/index', [
            'departments' => $departments,
        ]);
    }

    public function create()
    {
        return view('department.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name'=>'required',
        ]);
        // dd($request);
        Department::create(array_merge($request->all(), ['created_by'=>Auth::user()->id]));

        // return redirect(route('departments.index'))->with('success', 'Department created successfully.');

        return back()->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('department.create', ['department'=>$department]);
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'department_name'=>'required',
        ]);

        $department->update(array_merge($request->all(), ['updated_by'=>Auth::user()->id]));

        // return redirect(route('departments.index'))->with('success', 'Department updated successfully.');
        return back()->with('success', 'Department updated successfully.');
    }
}

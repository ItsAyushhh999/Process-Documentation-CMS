<?php

namespace App\Http\Controllers;

use App\Http\Services\PermissionService;
use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPermissionController extends Controller
{
    public function __construct(private PermissionService $permissionService)
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(User $user)
    {
        $userPermissions = PermissionUser::where('user_id', $user->id)->get();

        $projects = Project::select('*')
            ->with('subprojects')
            ->get(['id', 'name', 'sub_projects'])
            ->where('sub_projects', 0);

        $permissions = Permission::select('name', 'id')->get();

        // return view('permission.index', compact('user', 'projects', 'permissions', 'userPermissions'));
        // return view('permission.index', compact('user', 'projects', 'permissions'));
        // return Inertia::render('Permission/index', compact('user', 'projects', 'permissions'));
        return Inertia::render('Permission/index', [
        'user' => $user,
        'projects'=> $projects->values()->toArray(),
        'permissions'=> $permissions,
        'userPermissions'=> $userPermissions->toArray(),
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $permissions = $request->get('permissions', []);
        // dd($permissions);

        try {
            $this->permissionService->syncPermissions($user, $permissions);
        } catch (\Exception $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('success', 'Successfully updated permissions.');
    }
}

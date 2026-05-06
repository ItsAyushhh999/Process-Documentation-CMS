<?php

namespace App\Http\Controllers;

use App\Http\Traits\DepartmentCheck;
use App\Models\Department;
use App\Models\Title;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Inertia\Inertia;

class UserController extends Controller
{
    use DepartmentCheck;

    public function __construct()
    {
        $this->middleware('admin')->except('index', 'edit', 'update');
    }

    public function index(Request $request)
    // public function index()
    {
        $status = $request->get('status', '1');
        // dd($status);
        $start_from = now()->subMonths(3)->startOfDay();
        $end_to = now()->endOfDay();
        $users = User::select(
            'users.id as id',
            'users.department_id as department_id',
            'users.title_id as title_id',
            'name',
            'phone',
            'email',
            'updated_by',
            'slack_username',
            'github_username',
            'profile_picture',
            'feed_scope',
            'users.status as status',
            DB::raw('(SELECT departments.department_name  from departments WHERE departments.id = users.department_id )AS departments_name'),
            DB::raw('(SELECT titles.title_name from titles WHERE titles.id = users.title_id LIMIT 1)AS title_name'),
            DB::raw("(
            SELECT COUNT(*) FROM (
                SELECT DISTINCT collaborator, taskId
                FROM task_collaborators
                WHERE collaborator = users.id
                AND taskId IN (
                    SELECT id
                    FROM tasks
                    WHERE status NOT IN (5,11)
                    AND created_at BETWEEN '{$start_from}' AND '{$end_to}'
                )
            ) AS unique_collaborators
        ) AS task_count")
        )
            ->with('updatedBy')
            // ->when($status != 'all', fn ($query) => $query->where('status', $status))
           ->where('status', $status)
            ->orderby('name')
            ->get();
        $isDepartmentHead = $this->CheckDepartment(FacadesAuth::user()->id);
        $departments = Department::all();
        $titles = Title::all();

        // return view('user.index', ['users' => $users, 'isDepartmentHead' => $isDepartmentHead, 'status' => $status]);

        return Inertia::render('Users/index', [
           'users' => $users,
           'isDepartmentHead' => $isDepartmentHead,
           'status' => $status,
           'departments' => $departments,
           'titles' => $titles,
        ]);
    }

    public function edit(User $user)
    {
        $user->load(['department', 'title']);
        $departments = Department::all();
        $titles = Title::all();
        $isDepartmentHead = $this->CheckDepartment(FacadesAuth::user()->id);
        $checkUser = Auth::user()->id;
        if (!$isDepartmentHead && $checkUser != $user->id) {
            return back()->with('error', 'Not enough privileges.');
        }

        return view('user.user_department_title', [
            'user' => $user,
            'departments' => $departments,
            'titles' => $titles,
            'isDepartmentHead' => $isDepartmentHead,
        ]);
        //  $respone = [
        //     // 'code'    => 200,
        //     'status'  => 'success',
        //     'message' => 'User Data Fetched Successfully',
        //     'data'    => [
        //     'user' => $user,
        //     'departments' => $departments,
        //     'titles' => $titles,
        //     'isDepartmentHead' => $isDepartmentHead
        //     ]
        //     ];
        // return response()->json($respone);
    }

    public function update(Request $request, $user)
    {
        $request->validate([
            'department'     => 'required',
            'title'          => 'required',
            'phone'          => 'required|numeric|min:10',
            'slack_username' => 'required',
            'status'         => 'required|in:0,1',
            'feed_scope'  => 'required|in:0,1',
        ]);
        $user = User::selectRaw('users.* ,
        (SELECT `department_name` FROM `departments` WHERE `departments`.`id` = `users`.`department_id` LIMIT 1) AS departmentName')
        ->find($user);
        $department = explode(',', $request->department);
        if ($user->id != Auth::id() && !$this->CheckDepartment(FacadesAuth::user()->id)) {
            return back()->with('error', 'Not enough privileges.');
        }
        if ($user->id == Auth::id() && $user->departmentName != 'Project Administration' && $department[0] == 'Project Administration') {
            return back()->with('error', 'Not enough privileges.');
        }

        $user->department_id = $department[1];
        $user->title_id = $request->title;
        $user->phone = $request->phone;
        $user->slack_username = $request->slack_username;
        $user->github_username = $request->github_username;
        $user->status = (string) $request->status;
        $user->feed_scope = (string) $request->feed_scope;

        $user->updated_by = Auth::user()->id;
        $user->save();

        // return redirect(route('users.index'))->with('success', 'User updated successfully.');
        return back()->with('success', 'User updated successfully.');
    }
}

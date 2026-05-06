<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::User();
        $userId = $user->id;
        $projects = Project::with('subprojects')->latest()->get();
        $taskTypes = TaskType::latest()->get();
        $users = User::select('id', 'name', 'profile_picture')->latest()->get();

        $taskIds = TaskCollaborator::selectRaw('DISTINCT taskId')
            ->where('collaborator', $userId)
            ->pluck('taskId');

        // <<<<<<< HEAD
        $data = Task::selectRaw("id, title, created_at, priority, status, deadline, description,
           (SELECT name FROM users WHERE users.id = tasks.createdBy) AS createdBy,
           (select name from projects where projects.id = tasks.project_id) as projectName,
           (select flag from task_collaborators where task_collaborators.taskId = tasks.id And
            task_collaborators.collaborator = $userId And task_collaborators.flag = '0' limit 1) as isAssignee,
           (select flag from task_collaborators where task_collaborators.taskId = tasks.id And
            task_collaborators.collaborator = $userId And task_collaborators.flag = '1' limit 1) as isReviewer
       ")
        ->with(['collaborators' => function ($query) {
            $query->selectRaw('taskId, flag, collaborator, (select name from users where task_collaborators.collaborator = users.id ) as name, (select profile_picture from users where task_collaborators.collaborator = users.id ) as profile_picture');
        }])
        ->whereIn('id', $taskIds)
        ->whereIn('status', ['0', '1', '2', '3'])
        ->orderBy('priority', 'DESC')
        ->orderBy('id', 'DESC')
        ->get();
        // =======
        //         $data = Task::selectRaw("id, title, created_at, priority, status, deadline,
        //             (SELECT name FROM users WHERE users.id = tasks.createdBy) AS createdBy,
        //             (select name from projects where projects.id = tasks.project_id) as projectName,
        //             (select flag from task_collaborators where task_collaborators.taskId = tasks.id And
        //              task_collabmorators.collaborator = $userId And task_collaborators.flag = '0' limit 1) as isAssignee,
        //             (select flag from task_collaborators where task_collaborators.taskId = tasks.id And
        //              task_collaborators.collaborator = $userId And task_collaborators.flag = '1' limit 1) as isReviewer
        //         ")
        //         ->with(['collaborators' => function ($query) {
        //             $query->selectRaw('taskId, flag, collaborator, (select name from users where task_collaborators.collaborator = users.id ) as name ');
        //         }])
        //         ->whereIn('id', $taskIds)
        //         ->whereIn('status', ['0', '1', '2', '3'])
        //         ->orderBy('priority', 'DESC')
        //         ->orderBy('id', 'DESC')
        //         ->get();
        // >>>>>>> 89aa78a3d7508e6b70db74d512a20c5982ef5231

        //    dd($data);
        // dd($taskTypes);
        return Inertia::render('Dashboard/index', [
         'tasks' => $data,
         'users' => $users,
         'taskTypes' => $taskTypes,
         'projects' => $projects,
         'user_id' => $user->id,
         // 'data' => $data,
    ]);
        //        return view('dashboard', compact('data'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $taskAssignee = $request->get('taskAssignee', []);
        $taskReviewer = $request->get('taskReviewer', []);

        $taskPriority = $request->get('taskPriority', []);
        $taskProjects = $request->get('taskProjects', []);

        $user = Auth::User();
        $userId = $user->id;

        $projects = fn () => Project::select('id', 'name as group_name', 'name', 'sub_projects')
                                        ->where('sub_projects', '0')
                                        ->with('subprojects:id,name,sub_projects')->get();
        $taskTypes = fn () => TaskType::latest()->get();
        $users = fn () => User::select('id', 'name', 'profile_picture')->latest()->get();
        $taskIds = TaskCollaborator::select('taskId')
            ->where(function ($query) use ($userId) {
                $query->where('collaborator', $userId)
                    ->orWhere('createdBy', $userId);
            })
            ->distinct()
            ->pluck('taskId');

        $data = Task::selectRaw("id, title, created_at, priority, status, deadline, description,
           (SELECT name FROM users WHERE users.id = tasks.createdBy) AS createdBy,
           (select name from projects where projects.id = tasks.project_id) as projectName,
           (select flag from task_collaborators where task_collaborators.taskId = tasks.id And
            task_collaborators.collaborator = $userId And task_collaborators.flag = '0' limit 1) as isAssignee,
           (select flag from task_collaborators where task_collaborators.taskId = tasks.id And
            task_collaborators.collaborator = $userId And task_collaborators.flag = '1' limit 1) as isReviewer
       ")
            ->with(['collaborators' => function ($query) {
                $query->selectRaw('taskId, flag, collaborator, 
                (select name from users where task_collaborators.collaborator = users.id ) as name, 
                (select profile_picture from users where task_collaborators.collaborator = users.id ) as profile_picture');
            }])
            ->whereIn('id', $taskIds)
            ->whereIn('status', ['0', '1', '2', '3', '4', '5', '8', '9', '10', '11', '15', '16'])
            ->orderBy('priority', 'DESC')
            ->orderBy('id', 'DESC')
            ->when(!empty($taskAssignee), function ($query) use ($taskAssignee) { // filter based on task assigne
                $query->whereIn('id', function ($subQuery) use ($taskAssignee) {
                    $subQuery->select('taskId')
                        ->from('task_collaborators')
                        ->where('flag', '0')
                        ->whereIn('collaborator', $taskAssignee);
                });
            })
            ->when(!empty($taskReviewer), function ($query) use ($taskReviewer) { // filter based on task reviewer
                $query->whereIn('id', function ($subQuery) use ($taskReviewer) {
                    $subQuery->select('taskId')
                        ->from('task_collaborators')
                        ->where('flag', '1')
                        ->whereIn('collaborator', $taskReviewer);
                });
            })
            ->when(!empty($taskPriority), function ($query) use ($taskPriority) {
                $query->whereIn('priority', $taskPriority);
            })
            ->when(!empty($taskProjects), function ($query) use ($taskProjects) {
                $query->whereIn('project_id', $taskProjects);
            })
            ->get();

        return Inertia::render('Dashboard/index', [
            'tasks'        => $data,
            'users'        => Inertia::lazy(fn () => $users()),
            'taskTypes'    => Inertia::lazy(fn () => $taskTypes()),
            'projects'     => Inertia::lazy(fn () => $projects()),
            'user_id'      => $user->id,
            'taskAssignee' => $taskAssignee,
            'taskReviewer' => $taskReviewer,
            'taskPriority' => $taskPriority,
            'taskProjects' => $taskProjects,
        ]);
    }
}

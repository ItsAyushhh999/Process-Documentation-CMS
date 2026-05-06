<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DevopsFeedsController extends Controller
{
    public function index()
    {

        $userid = Auth::user()->id;

        $authorizedIds = config('app.authorized_id');

        if (!in_array($userid, $authorizedIds)) {
            return redirect('/dashboard')->with('error', 'Not enough privileges.');
        }

        $tasks = Task::selectRaw('tasks.*,
                (SELECT name FROM projects WHERE projects.id = tasks.project_id) AS projectName,
                (SELECT name FROM users WHERE users.id = tasks.createdBy) AS createdBy,
                (SELECT name FROM users WHERE tasks.assignedBy = users.id LIMIT 1) AS assignedBy,
                (SELECT name FROM users WHERE tasks.completedBy = users.id LIMIT 1) AS completedBy,
                (SELECT GROUP_CONCAT(type) FROM taskTypes
                    JOIN task_taskTypes_pivot ON taskTypes.id = task_taskTypes_pivot.taskTypes_id
                    WHERE task_taskTypes_pivot.task_id = tasks.id) AS types')
            ->with([
                'collaborators' => function ($query) {
                    $query->selectRaw('task_collaborators.id, task_collaborators.collaborator as collaborator_id, task_collaborators.taskId, task_collaborators.flag,
                        (SELECT name FROM users WHERE task_collaborators.collaborator = users.id) AS collaborator');
                },
                'taskStatus:id,value,name',
                'project.subprojects',
            ])
            ->where('status', '10')
            ->orderBy('id', 'DESC')
            ->get();

        return Inertia::render('DevopsFeeds/index', compact('tasks'));
    }
}

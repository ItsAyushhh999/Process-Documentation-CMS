<?php

namespace App\Http\Controllers;

use App\Http\Services\PermissionService;
use App\Http\Services\TaskService;
use App\Http\Traits\UserCheck;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskActivityLog;
use App\Models\TaskCollaborator;
use App\Models\TaskComment;
use App\Models\TaskStatus;
use App\Models\TaskStatusLog;
use App\Models\TaskType;
use App\Models\TaskTypePivot;
use App\Models\TaskWatchList;
use App\Models\User;
use App\Services\FileHandler;
use App\Services\NotificationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class TaskController extends Controller
{
    use UserCheck;

    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function _taskCollaboratorCreate($collaborator, $taskId, $flag = '0')
    {
        $user = Auth::user()->id;

        $TaskCollaborator = TaskCollaborator::firstOrCreate(
            [
                'taskId'       => $taskId,
                'collaborator' => $collaborator,
                'flag'         => $flag,
            ],
            [
                'taskId'       => $taskId,
                'collaborator' => $collaborator,
                'flag'         => $flag,
                'createdBy'    => $user,
                'updatedBy'    => $user,
            ]
        );

        if ($TaskCollaborator->wasRecentlyCreated) {
            return response(['collaborator' => $TaskCollaborator->collaborator]);
        }
    }

    /**
     * updates the collaborators for tasks.
     *
     * @param  mixed $taskId
     * @param  mixed $collaboratorIds
     * @param  mixed $flag
     * @return void
     */
    private function _taskCollaboratorUpdate($taskId, $collaboratorIds, $flag = '0')
    {

        $previousCollaborators = TaskCollaborator::where([['taskId', $taskId], ['flag', $flag]])
            ->pluck('collaborator')
            ->toArray();

        if ($previousCollaborators) {
            if (!count($collaboratorIds)) {
                throw new Exception('Removing the last remaining assignee is not allowed.');
            }

            $removeCollaborators = array_values(array_diff($previousCollaborators, $collaboratorIds));

            TaskCollaborator::where([['taskId', $taskId], ['flag', $flag]])
                ->whereIn('collaborator', $removeCollaborators)
                ->delete();
        }

        $newCollaborators = [];
        foreach ($collaboratorIds as $id) {
            $collaborator = ($this->_taskCollaboratorCreate($id, $taskId, $flag));
            if ($collaborator) {
                $newCollaborators[] = json_decode($collaborator->getContent())->collaborator;
            }
        }
        if (count($newCollaborators)) {
            return response(['collaborators' => $newCollaborators]);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $routeFlag = false; // true when draft list
        $pastDate = now()->subMonths(3)->startOfDay();
        $presentDate = now()->endOfDay();

        $start_from = $request->star_from ? Carbon::parse($request->star_from) : $pastDate;
        $end_to = $request->end_to ? Carbon::parse($request->end_to) : $presentDate;

        // dd($start_from,"keshab",$end_to);
        $tasks = Task::selectRaw('tasks.id, title, status,
        (SELECT name FROM projects WHERE projects.id = tasks.project_id) AS projectName ,
        (SELECT name FROM users WHERE users.id = tasks.createdBy) AS createdBy,
        (SELECT name FROM users WHERE tasks.assignedBy = users.id LIMIT 1) AS assignedBy,
        (SELECT name FROM users WHERE tasks.completedBy = users.id LIMIT 1) AS completedBy,
        DATEDIFF((`completedAt`), (`assignedAt`)) as completed,
        (SELECT GROUP_CONCAT(type) FROM taskTypes JOIN task_taskTypes_pivot ON taskTypes.id = task_taskTypes_pivot.taskTypes_id WHERE task_taskTypes_pivot.task_id = tasks.id) as types');

        if ($request->route()->getName() == 'tasks.index') {

            $tasks = $tasks->whereHas('collaborators', function ($query) {
                $query->where('flag', '0');
            })
                ->whereNotIn('status', ['6', '7'])
                ->whereBetween('created_at', [$start_from, $end_to]);
        } else { // draft task
            if (!$this->checkUser($user)) {
                return back()->with('error', 'Not enough privileges.');
            } //only admin's section
            $routeFlag = true;

            $tasks = $tasks->where(function ($query) use ($user) {
                $query->where('createdBy', $user->id)
                    ->orWhereHas('collaborators', function ($query) use ($user) {
                        $query->where([['flag', '1'], ['collaborator', $user->id]]);
                    });
            })
                ->where('status', '7'); // 7: draft
        }

        $tasks = $tasks->with(['collaborators' => function ($query) {
            $query->selectRaw('task_collaborators.id, task_collaborators.collaborator,task_collaborators.taskId,task_collaborators.flag,
            (SELECT name FROM users WHERE task_collaborators.collaborator = users.id) AS collaborator');
        }, 'taskStatus:id,value,name', 
            'githubWebhooks:id,task_id,pull_request_id,pull_request_title,status,pull_request_url,sender_username,repository_name,created_at'])
            ->orderBy('id', 'DESC');

        $projects = fn () => Project::select('id', 'name as group_name', 'name', 'sub_projects')
            ->where('sub_projects', '0')
            ->with(['subprojects:id,name,sub_projects'])->get();

        return Inertia::render('Tasks/index', [
            'tasks'       => fn () => $tasks->get(),
            'routeFlag'   => fn () => $routeFlag,
            'start_from'  => fn () => $start_from,
            'end_to'      => fn () => $end_to,
            'projects'    => Inertia::lazy(fn () => $projects()),
            'task_types'  => Inertia::lazy(fn () => TaskType::get(['id', 'type'])),
            'users'       => Inertia::lazy(fn () =>  User::where('status', '1')->get(['id', 'name'])),
        ]);
    }

    //public function show(Task $task)
    //{
    //    $task->load(['attachment', 'taskType']); //lazy eager loading
    //    return view('task.details', ['task' => $task]);
    //}

    public function create()
    {
        $projects = Project::get(['id', 'name', 'sub_projects']);
        $task_types = TaskType::all();
        $users = User::where('status', '1')->get(['id', 'name']);

        return view('task.create', compact('projects', 'users', 'task_types'));
    }

    /**
     * stores and assigns new tasks to user.
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request, NotificationService $notificationService)
    {

        $user = Auth::user();
        if (!$this->checkUser($user)) {
            return back()->with('error', 'Not enough privileges.');
        }
        $userName = explode(' ', $user->name);
        $userName = $userName[0] . ' ' . substr($userName[1], 0, 1);

        $this->validate($request, [
            'title'         => 'required',
            'description'   => 'required',
            'reviewer'      => 'required',
            'reviewer.*'    => 'required',
            'title'         => 'required',
            'project_id'    => 'required',
            'attachments'   => 'nullable|array|max:10',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,csv,xls,xlsx,txt,docx',
            'task_type'     => 'required',
            'deadline'      => 'required',
        ]);

        // dd($request);

        $assignees = $request->assignees ?? [];
        $reviewers = $request->reviewer ?? [];

        $status = count($assignees) ? '0' : '7'; // 0: assigned 7: created
        $task = Task::create([
            'title'       => $request->title,
            'description' => $request->description,
            'project_id'  => $request->project_id,
            'priority'    => $request->priority ?? '0',
            'deadline'    => Carbon::parse($request->deadline),
            'createdBy'   => $user->id,
            'updatedBy'   => $user->id,
            'status'      => $status,
            'assignedAt'  => count($assignees) ? Carbon::now() : null,
            'assignedBy'  => count($assignees) ? $user->id : 0,
        ]);

        // add task id and task type in task_tasktype_pivot
        if (isset($request->task_type) && count($request->task_type)) {
            $task->taskType()->attach($request->task_type, [
                'createdBy' => $user->id,
                'updatedBy' => $user->id,
            ]);
        }
        if ($request->hasFile('attachments')) {
            $data = FileHandler::file_store($request->attachments);
            AttachmentController::store($data, $task->id);
        }

        foreach ($assignees as $assigee) {
            $this->_taskCollaboratorCreate($assigee, $task->id);
        } //creates assignees for task

        foreach ($reviewers as $reviewer) {
            $this->_taskCollaboratorCreate($reviewer, $task->id, '1');
        } //creates reviewers for task

        app(TasksCommentController::class)->event('status', $task);

        $users = User::whereIn('id', array_merge($assignees, $reviewers))->get();

        $collaboratorFlag = Task::with(['collaborators' => function ($query) {
            $query->selectRaw('task_collaborators.id, task_collaborators.collaborator,task_collaborators.taskId,task_collaborators.flag,
            (SELECT name FROM users WHERE task_collaborators.collaborator = users.id) AS collaborator,
            (SELECT email FROM users WHERE task_collaborators.collaborator = users.id) AS email');
        }])->find($task->id);
        // return $collaboratorFlag;

        $reviewer = $collaboratorFlag->collaborators->where('flag', '1');
        $assignee = $collaboratorFlag->collaborators->where('flag', '0');
        $creator = \Auth::user()->email;

        $assigneUsers = $assignee->pluck('email')->toArray();
        $reviewerUsers = $reviewer->pluck('email')->toArray();
        $usersToNotify = array_merge($assigneUsers, $reviewerUsers);
        $usersToNotify = array_unique($usersToNotify);
        $message = "You have been assigned to a new task: $task->title";
        $title = 'New Task Created';

        $notificationService->notifyUser($usersToNotify, $title, $message, config('app.url') . ('/tasks/' . $task->id . '/edit'));

        $token = config('app.bot_token');

        try {
            $creatorMessage = app(TaskService::class)->generateNewTaskBlock($task, 'YOU HAVE CREATED A NEW TASK');
            Http::withHeaders([
                'Authorization' => "Bearer $token",
            ])->post('https://slack.com/api/chat.postMessage', [
                'channel' => '@' . $user->slack_username,
                'blocks'  => $creatorMessage,
                // 'text'  => "You Have been marked as Creator for this Task: $request->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
            ]);

            Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => ['2']], function ($message) use ($creator, $request, $userName) {
                $message->from(config('mail.from.address'), $userName);
                $message->to($creator);
                $message->subject("New task `$request->title` Created.");
            });
        } catch (Exception $e) {
            // dd($e->getMessage());
            // return back()->with('error', 'Error sending mail');
        }

        try {

            $rChannels = User::whereIn('email', $reviewer->pluck('email')->toArray())->whereNotNull('slack_username')->get();
            $reviewerMessage = app(TaskService::class)->generateNewTaskBlock($task, 'YOU HAVE BEEN MARKED AS REVIEWER');
            foreach ($rChannels as $rChannel) {
                Http::withHeaders([
                    'Authorization' => "Bearer $token",
                ])->post('https://slack.com/api/chat.postMessage', [
                    'channel' => '@' . $rChannel->slack_username,
                    // 'text'  => "You Have been marked as Reviewer for Task: $request->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                    'blocks' => $reviewerMessage,
                ]);
            }

            Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($reviewer->toArray())], 
                function ($message) use ($reviewer, $request, $userName) {
                    $message->from(config('mail.from.address'), $userName);
                    $message->to($reviewer->pluck('email')->toArray());
                    $message->subject("New task `$request->title` Created.");
            });
        } catch (Exception $e) {
            // dd($e->getMessage());
            // return back()->with('error', 'Error sending mail');
        }

        try {
            if (count($assignee) > 0) {
                $assigneeMessage = app(TaskService::class)->generateNewTaskBlock($task);
                $aChannels = User::whereIn('email', $assignee->pluck('email')->toArray())->whereNotNull('slack_username')->get();

                foreach ($aChannels as $aChannel) {
                    Http::withHeaders([
                        'Authorization' => "Bearer $token",
                    ])->post('https://slack.com/api/chat.postMessage', [
                        'channel' => '@' . $aChannel->slack_username,
                        'blocks' => $assigneeMessage,
                        // 'text'  => "You have been assigned to a Task: ($request->title). More details at => https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                    ]);
                }

                Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($assignee->toArray())], function ($message) use ($assignee, $request, $userName) {
                    $message->from(config('mail.from.address'), $userName);
                    $message->to($assignee->pluck('email')->toArray());
                    $message->subject("New task `$request->title` Created.");
                });
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
            // return back()->with('error', 'Error sending mail');
        }

        app(NotificationController::class)->create([
            'notification' => 'New task has been created.',
            'taskId'       => $task->id,
        ]);

        return redirect(route('tasks.index'))->with('success', 'Task added Successfully.'); 
    }

    public function edit($taskId)
    {
        $user = Auth::user();
        $isSuperAdmin = $this->checkUser($user);
        $task_types = TaskType::all();
        $task = Task::selectRaw(
            'tasks.*,
            (SELECT name FROM projects WHERE tasks.project_id = projects.id LIMIT 1) as projectName,
            (SELECT name FROM users WHERE tasks.createdBy = users.id LIMIT 1) AS createdBy,
            (SELECT name FROM users WHERE tasks.updatedBy = users.id LIMIT 1) AS updatedBy,
            (SELECT profile_picture FROM users WHERE tasks.createdBy = users.id LIMIT 1) AS creatorImage,
            (SELECT profile_picture FROM users WHERE tasks.updatedBy = users.id LIMIT 1) AS updaterImage,
            (SELECT name FROM task_statuses WHERE tasks.status = task_statuses.value LIMIT 1) AS taskStatus'
        )->with([
            'project.subprojects:id,sub_projects,name,repository_name', // Loads subprojects related to the task's project
            'project.parentProject.subprojects:id,sub_projects,name,repository_name', // loads the subprojects project of the parent projects
        ])
            ->find($taskId);
        
        if (!$task) {
            return back();
        }
        
        $creatorId = \App\Models\Task::where('id', $taskId)
                            ->value('createdBy');

        $taskStatus = TaskStatus::select('id', 'name', 'value')->where('status', '1')->orderBy('listOrder', 'asc')->get();

        $collaborators = TaskCollaborator::select('task_collaborators.*', 'users.name as collaboratorName', 'users.profile_picture as collaboratorImage')
            ->where('taskId', $taskId)
            ->join('users', 'users.id', 'task_collaborators.collaborator')
            ->get();

        $auth_user = auth()->user();

        $assignees = $collaborators->where('flag', '0')->values();
        $reviewers = $collaborators->where('flag', '1')->values();
        $assigneesIds = $assignees->pluck('collaborator')->toArray();

        $reviewersIds = array_merge($reviewers->pluck('collaborator')->toArray(), [3]);

        $permissions = array_unique(array_merge($reviewersIds, [$task->assignedBy]));

        // Check if deployment permission is granted based on whether the authenticated user is a collaborator or the creator of the task.
        $deploy_permission_granted = fn () => ($auth_user && (in_array($auth_user->email, $collaborators->pluck('email')->toArray()) || auth()->user()->name == $task->createdBy));

        $attachments = fn () => Attachment::where([['task_id', $taskId], ['commentId', '0']])->get();

        $comments = TaskComment::selectRaw('task_comments.*,
            (SELECT name FROM users WHERE users.id = task_comments.pinnedBy) as pinnedBy,
            (SELECT name FROM users WHERE users.id = task_comments.createdBy) as user,
            (SELECT name FROM users WHERE users.id = task_comments.checkedBy) as checkedBy,
            (SELECT profile_picture FROM users WHERE users.id = task_comments.createdBy) as profie_picture')

            ->with([
                'getCommentImage' => function ($query) {
                    $query->select('name', 'commentId')->get();
                },
                'replies' => function ($query) {
                    $query->with('getReplyImage', 'reply_creator', 'checked_by')->orderBy('id', 'ASC')->get();
                },
            ])
            ->where('taskId', $taskId)
            ->where('reply_id', 0)
            ->orderBy('id', 'DESC')
            ->get();

        $task->total_pinned_comments = $comments->where('isPinned', '1')->count();

        // get permissions of user
        $userPermissions = $this->permissionService->permissionNameByProject($task->project_id);

        $isAsignee = in_array(Auth::id(), $assigneesIds);
        $isReviewer = in_array(Auth::id(), $reviewersIds);

        $taskStatus = fn () => $this->_getStatuses($task, $isAsignee, $isReviewer, $isSuperAdmin);
        
        return Inertia::render('Tasks/edit', [
            'task'                      => $task,
            'users'                     => fn () => User::where('status', '1')->get(['id', 'name']),
            'deploy_permission_granted' => fn () => $deploy_permission_granted(),
            'assignees'                 => $assignees,
            'reviewers'                 => $reviewers,
            'attachments'               => fn () => $attachments,
            'assigneesIds'              => $assigneesIds,
            'reviewersIds'              => $reviewersIds,
            'isSuperAdmin'              => $isSuperAdmin,
            'task_types'                => $task_types,
            'task_typeIds'              => fn () => TaskTypePivot::where('task_id', $taskId)->pluck('taskTypes_id'),
            'taskStatus'                => fn () => $taskStatus(),
            'userPermissions'           => $userPermissions,
            'permissions'               => $permissions,
            'collaborators'             => $collaborators,
            'isReview'                  => fn () => in_array(auth()->user()->id, $permissions),
            'watchListTask'             => fn () => TaskWatchList::where('user_id', Auth::user()->id)->where('task_id', $taskId)->exists(),
        ]);
    }

    // need to update later
    public function editV2($taskId)
    {
        $user = Auth::user();
        $isSuperAdmin = $this->checkUser($user);
        $task_types = TaskType::all();
        $task = Task::selectRaw('tasks.*,
        (SELECT name FROM projects WHERE tasks.project_id = projects.id LIMIT 1) as projectName,
        (SELECT name FROM users WHERE tasks.createdBy = users.id LIMIT 1) AS createdBy,
        (SELECT name FROM users WHERE tasks.updatedBy = users.id LIMIT 1) AS updatedBy')
            ->find($taskId);

        $users = User::all();

        $collaborators = TaskCollaborator::select('task_collaborators.*', 'users.name as collaboratorName')
            ->where('taskId', $taskId)
            ->join('users', 'users.id', 'task_collaborators.collaborator')
            ->get();
        $task_typeIds = [];
        $task_type = TaskTypePivot::where('task_id', '=', $taskId)->get('taskTypes_id');
        foreach ($task_type as $type) {
            $task_typeIds[] = $type->taskTypes_id;
        }

        $auth_user = auth()->user();
        $projectId = Project::where('id', $task->project_id)->pluck('sub_projects')->first();
        if ($projectId == 21) {
            $deploy_permission = ['saroj.s@shikhartech.com', 'danshwaraj.c@shikhartech.com'];
        } else {
            $deploy_permission = ['danshwaraj.c@shikhartech.com'];
        }

        $deploy_permission_granted = ($auth_user && in_array($auth_user->email, $deploy_permission));

        $assignees = $collaborators->where('flag', '0');
        $reviewers = $collaborators->where('flag', '1');
        $assigneesIds = $assignees->pluck('collaborator')->toArray();

        $reviewersIds = $reviewers->pluck('collaborator')->toArray();

        $attachments = Attachment::where([['task_id', $taskId], ['commentId', '0']])->get();

        $comments = TaskComment::selectRaw('task_comments.*,
            (SELECT name FROM users WHERE users.id = task_comments.pinnedBy) as pinnedBy,
            (SELECT name FROM users WHERE users.id = task_comments.createdBy) as user,
            (SELECT name FROM users WHERE users.id = task_comments.checkedBy) as checkedBy,
            (SELECT profile_picture FROM users WHERE users.id = task_comments.createdBy) as profie_picture')

            ->with([
                'getCommentImage' => function ($query) {
                    $query->select('name', 'commentId')->get();
                },
                'replies' => function ($query) {
                    $query->with('getReplyImage', 'reply_creator', 'checked_by')->orderBy('id', 'ASC')->get();
                },
            ])
            ->where('taskId', $taskId)
            ->where('reply_id', 0)
            ->orderBy('id', 'DESC')
            ->get();

        $taskStatusLogs = TaskStatusLog::where('taskId', $task->id)->selectRaw('task_status_logs.*,
        (SELECT name FROM users WHERE task_status_logs.createdBy = users.id) AS createdBy')
            ->orderBy('id', 'DESC')->get();

        $taskActivityLogs = TaskActivityLog::where('taskId', $task->id)->selectRaw(
            'task_activity_logs.*,
        (SELECT name FROM users WHERE task_activity_logs.createdBy = users.id) AS createdBy,
        (SELECT name FROM users WHERE task_activity_logs.oldId = users.id ) AS removedUser,
        (SELECT name FROM users WHERE task_activity_logs.newId = users.id ) AS addedUser,
        (SELECT type FROM  taskTypes WHERE task_activity_logs.newId = taskTypes.id ) AS addedTaskTypes,
        (SELECT type FROM  taskTypes WHERE task_activity_logs.oldId = taskTypes.id ) AS removedTaskTypes
        '
        )
            ->orderBy('id', 'DESC')->get();
        $activities = $taskStatusLogs->concat($comments)->concat($taskActivityLogs)->sortByDesc('created_at');

        $projects = Project::select('id', 'name as group_name', 'name', 'sub_projects')->where('sub_projects', '0')->with('subprojects')->get();

        $isAsignee = in_array(Auth::id(), $assigneesIds);
        $isReviewer = in_array(Auth::id(), $reviewersIds);

        $taskStatus = $this->_getStatuses($task, $isAsignee, $isReviewer, $isSuperAdmin);

        return response()->json([
            'task'                      => $task,
            'users'                     => $users,
            'deploy_permission_granted' => $deploy_permission_granted,
            'assignees'                 => $assignees->values(),
            'reviewers'                 => $reviewers->values(),
            'attachments'               => $attachments,
            'projects'                  => $projects->toArray(),
            'assigneesIds'              => $assigneesIds,
            'reviewersIds'              => $reviewersIds,
            'isSuperAdmin'              => $isSuperAdmin,
            'task_types'                => $task_types,
            'task_typeIds'              => $task_typeIds,
            'activities'                => $activities->values(),
            'taskStatus'                => $taskStatus,
        ]);
    }

    public function editV3($taskId)
    {
        $user = Auth::user();
        $isSuperAdmin = $this->checkUser($user);
        $task_types = TaskType::all();
        $task = Task::selectRaw(
            'tasks.*,
        (SELECT name FROM projects WHERE tasks.project_id = projects.id LIMIT 1) as projectName,
        (SELECT name FROM users WHERE tasks.createdBy = users.id LIMIT 1) AS createdBy,
        (SELECT name FROM users WHERE tasks.updatedBy = users.id LIMIT 1) AS updatedBy,
        (SELECT profile_picture FROM users WHERE tasks.createdBy = users.id LIMIT 1) AS creatorImage,
        (SELECT profile_picture FROM users WHERE tasks.updatedBy = users.id LIMIT 1) AS updaterImage'
        )

            ->find($taskId);

        $users = User::all();

        $collaborators = TaskCollaborator::select('task_collaborators.*', 'users.name as collaboratorName', 'users.profile_picture as collaboratorImage')
            ->where('taskId', $taskId)
            ->join('users', 'users.id', 'task_collaborators.collaborator')
            ->get();
        $task_typeIds = [];
        $task_type = TaskTypePivot::where('task_id', '=', $taskId)->get('taskTypes_id');
        foreach ($task_type as $type) {
            $task_typeIds[] = $type->taskTypes_id;
        }

        $assignees = $collaborators->where('flag', '0');
        $reviewers = $collaborators->where('flag', '1');
        $assigneesIds = $assignees->pluck('collaborator')->toArray();

        $reviewersIds = $reviewers->pluck('collaborator')->toArray();

        $attachments = Attachment::where([['task_id', $taskId], ['commentId', '0']])->get();

        $isAsignee = in_array(Auth::id(), $assigneesIds);
        $isReviewer = in_array(Auth::id(), $reviewersIds);

        $taskStatus = $this->_getStatuses($task, $isAsignee, $isReviewer, $isSuperAdmin);

        return response()->json([
            'task' => $task,
            'users' => $users,
            'assignees' => $assignees->values(),
            'reviewers' => $reviewers->values(),
            'attachments' => $attachments,
            'isSuperAdmin' => $isSuperAdmin,
            'task_types' => $task_types,
            'task_typeIds' => $task_typeIds,
            'taskStatus' => $taskStatus,
        ]);
    }

    public function comments($taskId, Request $request)
    {
        $perPage = 20;
        $page = $request->get('page', 1);
        $offset = ($page -1) * $perPage;

        $comments = TaskComment::selectRaw(
            'task_comments.id, task_comments.taskId, task_comments.comments, task_comments.createdBy, task_comments.isPinned, 
            task_comments.pinnedBy, task_comments.check, task_comments.checkedBy, task_comments.created_at,
            (SELECT name FROM users WHERE users.id = task_comments.pinnedBy) as pinnedBy,
            (SELECT name FROM users WHERE users.id = task_comments.createdBy) as user,
            (SELECT name FROM users WHERE users.id = task_comments.checkedBy) as checkedBy,
            (SELECT profile_picture FROM users WHERE users.id = task_comments.createdBy) as profile_picture'
        )
            ->with([
                'getCommentImage:id,name,commentId',
                'replies' => function ($query) {
                    $query->select('id', 'taskId', 'reply_id', 'comments', 'createdBy', 'check', 'checkedBy', 'created_at')
                        ->with('getReplyImage:id,name,commentId', 
                                'reply_creator:id,name,profile_picture', 
                                'checked_by:id,name')
                        ->orderBy('id', 'ASC');
                },
            ])
            ->where('task_comments.taskId', $taskId)
            ->where('task_comments.reply_id', 0)
            ->orderByDesc('task_comments.id')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $taskStatusLogs = TaskStatusLog::selectRaw(
            'task_status_logs.id, task_status_logs.taskId, task_status_logs.previousStatus, task_status_logs.currentStatus, task_status_logs.created_at,
            (SELECT name FROM users WHERE users.id = task_status_logs.createdBy) as createdBy'
        )
            ->where('task_status_logs.taskId', $taskId)
            ->orderByDesc('task_status_logs.id')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $taskActivityLogs = TaskActivityLog::selectRaw(
                'task_activity_logs.id, task_activity_logs.taskId,
                task_activity_logs.action, task_activity_logs.property,
                task_activity_logs.oldId, task_activity_logs.newId,
                task_activity_logs.oldValue, task_activity_logs.newValue,
                task_activity_logs.created_at,
                (SELECT name FROM users WHERE users.id = task_activity_logs.createdBy) AS createdBy,
                (SELECT name FROM users WHERE users.id = task_activity_logs.oldId) AS removedUser,
                (SELECT name FROM users WHERE users.id = task_activity_logs.newId) AS addedUser,
                (SELECT type FROM taskTypes WHERE taskTypes.id = task_activity_logs.newId) AS addedTaskTypes,
                (SELECT type FROM taskTypes WHERE taskTypes.id = task_activity_logs.oldId) AS removedTaskTypes'
            )
                ->where('task_activity_logs.taskId', $taskId)
                ->orderByDesc('task_activity_logs.id')
                ->limit($perPage)
                ->offset($offset)
                ->get();

        // Combine and sort all activities by created_at
        $activities = $taskStatusLogs->concat($comments)->concat($taskActivityLogs)->sortByDesc('created_at')->values();

        return response()->json([
            'activities'            => $activities,
            'total_pinned_comments' => $comments->where('isPinned', '1')->count(),
            'has_more'              => $comments->count() === $perPage, // Indicates if there are more records to load
            'page'                  => $page,
        ]);
    }

    private function _getStatuses($task, $isAsignee, $isReviewer, $isSuperAdmin)
    {

        $onlyReviewerAllowedStatuses = [
            'Live - Ready to upload',
            'Live - Uploaded',
            'Staging - Ready to upload',
            'Staging - Uploaded',
            'Dev - Ready to upload',
            'Dev - uploaded',
        ];
        $hasDevlopmentPermission = true;
        $hasStagingPermission = true;
        $hasProductionPermission = true;

        $taskStatus = TaskStatus::select('id', 'name', 'value')->where('status', '1')->orderBy('listOrder', 'asc')->get();

        foreach ($taskStatus as $status) {
            $isDisabled = false;

            if (in_array($status->value, [0, 1, 2])) {
                if (!$isReviewer && $status->value == 0 && !$isSuperAdmin) {
                    $isDisabled = true;
                }
                if (!$isAsignee && $status->value == 1 && !$isSuperAdmin) {
                    $isDisabled = true;
                }
                if (($task->status != 1 || !$isAsignee && !$isSuperAdmin) && $status->value == 2) {
                    $isDisabled = true;
                }
            }

            if ($isSuperAdmin || $isReviewer) {
                if (in_array($status->value, [3, 4, 6, 7])) {
                    if ($status->value == 7) {
                        $isDisabled = true;
                    }
                    if (!$isSuperAdmin && !$isReviewer && in_array($status->value, [3, 4])) {
                        $isDisabled = true;
                    }
                }
            }

            if (in_array($status->name, $onlyReviewerAllowedStatuses)) {
                if (!$isReviewer) {
                    $isDisabled = true;
                }
                if (!$hasStagingPermission && in_array($status->name, ['Staging - Ready to upload', 'Staging - Uploaded'])) {
                    $isDisabled = true;
                }
                if (!$hasDevlopmentPermission && in_array($status->name, ['Dev - Ready to upload', 'Dev - uploaded'])) {
                    $isDisabled = true;
                }
                if (!$hasProductionPermission && in_array($status->name, ['Live - Ready to upload', 'Live - Uploaded'])) {
                    $isDisabled = true;
                }
            }

            if (
                $isSuperAdmin && !in_array($status->value, [0, 1, 2, 3, 4, 6, 7]) && !in_array($status->name, $onlyReviewerAllowedStatuses)
            ) {
                $isDisabled = false;
            }

            if (!$isDisabled || auth()->id() == 3) {
                $status->disabled = false;
            } else {
                $status->disabled = true;
            }

            $availableStatuses[] = $status;
        }

        return $availableStatuses;
    }

    /**
     * updates details of tasks.
     *
     * @param  mixed $request
     * @param  mixed $task
     * @return void
     */
    public function update(Request $request, Task $task, NotificationService $notificationService)
    {
        $user = Auth::user();

        $userName = explode(' ', $user->name);
        $userName = $userName[0] . ' ' . substr($userName[1], 0, 1);

        if (!$this->checkUser($user)) {
            return back()->with('error', 'Not enough privileges.');
        }

        $this->validate($request, [
            'title'       => 'required',
            'description' => 'required',
            'reviewers'   => 'required',
            'reviewers.*' => 'required',
            'title'       => 'required',
            'project_id'  => 'required',
            'task_type'   => 'required',
            'deadline'    => 'required',
        ]);

        // if ($request->status == '0')
        //     $this->validate($request, [
        //         'assignees'   => 'required',
        //         'assignees.*' => 'required',
        //     ]);

        $status = (isset($request->assignees) && $task->status == '7') ? '0' : $task->status; // 0: assigned 7: created

        DB::beginTransaction();
        try {
            $task->Update([
                'title'       => $request->title,
                'description' => $request->description,
                'deadline'    => Carbon::parse($request->deadline),
                'project_id'  => $request->project_id,
                'priority'    => $request->priority,
                'status'      => $status,
                'updatedBy'   => $user->id,
                'updated_at'  => Carbon::now(),
            ]);

            /* if ($request->status == '0' || $request->status == '1') {
                $task->completedAt = null;
                $task->completedBy = 0;
                $task->save();
            } elseif (!$task->assignedAt && isset($request->assignees)) {
                $task->assignedAt = Carbon::now();
                $task->assignedBy = $user->id;

                $task->save();
            } elseif (!$task->completedAt && $request->status == '2') { //assigned for review status
                $task->completedAt = Carbon::now();
                $task->completedBy = $user->id;

                $task->save();
            } */
            // if ($request->hasFile('attachments')) {
            //     $data =  FileHandler::file_store($request->attachments);
            //     AttachmentController::store($data, $task->id);
            // }

            $collaboratorFlag = Task::with(['collaborators' => function ($query) {
                $query->selectRaw('task_collaborators.id, task_collaborators.collaborator,task_collaborators.taskId,task_collaborators.flag,
                (SELECT name FROM users WHERE task_collaborators.collaborator = users.id) AS collaborator,
                (SELECT email FROM users WHERE task_collaborators.collaborator = users.id) AS email');
            }])->find($task->id);

            $reviewer = $collaboratorFlag->collaborators->where('flag', '1');
            $assignee = $collaboratorFlag->collaborators->where('flag', '0');

            $assignees = $this->_taskCollaboratorUpdate($task->id, $request->assignees ?? []); //updates assignees of the task
            $reviewers = $this->_taskCollaboratorUpdate($task->id, $request->reviewers ?? [], '1'); // updates reviewer for the task

            $newAssignees = $newReviewers = [];
            if ($assignees) {
                $newAssignees = json_decode($assignees->getContent())->collaborators;
            }
            if ($reviewers) {
                $newReviewers = json_decode($reviewers->getContent())->collaborators;
            }
            if (isset($request->task_type) && count($request->task_type)) {
                $taskTypeData = collect($request->task_type)->mapWithKeys(function ($taskType) use ($user) {
                    return [$taskType => [
                        'createdBy' =>  $user->id,
                        'updatedBy' =>  $user->id,
                    ]];
                });
                $task->taskType()->sync($taskTypeData);
            }

            DB::commit();

            // send web notification
            $taskCollaborators = TaskCollaborator::where('taskId', $task->id)->pluck('collaborator')->toArray();
            $usersToNotify = User::whereIn('id', $taskCollaborators)->pluck('email')->toArray();
            $message = "{$user->name} has made an update in task.";
            $title = 'Task Updated';

            $notificationService->notifyUser($usersToNotify, $title, $message, config('app.url') . ('/tasks/' . $task->id . '/edit'));

            $token = env('SLACK_API_TOKEN');

            if (count($newAssignees) || count($newReviewers)) {
                $collaborators = User::whereIn('id', array_merge($newAssignees, $newReviewers))->get();

                $newAssignees = $collaborators->whereIn('id', $newAssignees)->pluck('email')->toArray();
                $newReviewers = $collaborators->whereIn('id', $newReviewers)->pluck('email')->toArray();
                $creator = \Auth::user()->email;

                $token = config('app.bot_token');

                try {
                    if ($newAssignees) {

                        $aChannels = $collaborators->pluck('slack_username')->toArray();
                        $assigneeMessage = app(TaskService::class)->generateNewTaskBlock($task);

                        foreach ($aChannels as $aChannel) {
                            Http::withHeaders([
                                'Authorization' => "Bearer $token",
                            ])->post('https://slack.com/api/chat.postMessage', [
                                'channel' => '@' . $aChannel,
                                'blocks' => $assigneeMessage,
                                // 'text'  => "You have been assigned to a Task: $request->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                            ]);
                        }
                    }

                    if (count($newAssignees)) {
                        Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($assignee->toArray())], function ($message) use ($newAssignees, $request, $userName) {
                            $message->from(config('mail.from.address'), $userName);
                            $message->to($newAssignees);
                            $message->subject("New task `$request->title` Created.");
                        });
                    }
                } catch (Exception $e) {
                    // dd($e->getMessage());
                    return back()->with('error', 'Error sending mail');
                }

                try {
                    if ($newReviewers) {

                        $rChannels = $collaborators->pluck('slack_username')->toArray();
                        $reviewerMessage = app(TaskService::class)->generateNewTaskBlock($task, 'YOU HAVE BEEN MARKED AS REVIEWER');

                        foreach ($rChannels as $rChannel) {
                            Http::withHeaders([
                                'Authorization' => "Bearer $token",
                            ])->post('https://slack.com/api/chat.postMessage', [
                                'channel' => '@' . $rChannel,
                                'blocks' => $reviewerMessage,
                                // 'text'  => "You Have been marked as Reviewer for Task: $request->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                            ]);
                        }
                    }

                    if (count($newReviewers)) {
                        Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($reviewer->toArray())], function ($message) use ($newReviewers, $request, $userName) {
                            $message->from(config('mail.from.address'), $userName);
                            $message->to($newReviewers);
                            $message->subject("New task `$request->title` Created.");
                        });
                    }
                } catch (Exception $e) {
                    // dd($e->getMessage());
                    return back()->with('error', 'Error sending mail');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Task details updated successfully.');
    }

    public function updateV2(Request $request, Task $task)
    {
        $user = Auth::user();

        $userName = explode(' ', $user->name);
        $userName = $userName[0] . ' ' . substr($userName[1], 0, 1);

        if (!$this->checkUser($user)) {
            return back()->with('error', 'Not enough privileges.');
        }

        // Run the validation
        $request->validate([
            'title'       => 'required',
            'description' => 'required',
            'reviewers'   => 'required',
            'reviewers.*' => 'required',
            'project_id'  => 'required',
            'task_type'   => 'required',
            'deadline'    => 'required',
        ]);

        $status = (isset($request->assignees) && $task->status == '7') ? '0' : $task->status; // 0: assigned 7: created

        DB::beginTransaction();
        try {
            $task->Update([
                'title'       => $request->title,
                'description' => $request->description,
                'deadline'    => Carbon::parse($request->deadline),
                'project_id'  => $request->project_id,
                'priority'    => $request->priority,
                'status'      => $status,
                'updatedBy'   => $user->id,
                'updated_at'  => Carbon::now(),
            ]);

            $collaboratorFlag = Task::with(['collaborators' => function ($query) {
                $query->selectRaw('task_collaborators.id, task_collaborators.collaborator,task_collaborators.taskId,task_collaborators.flag,
                (SELECT name FROM users WHERE task_collaborators.collaborator = users.id) AS collaborator,
                (SELECT email FROM users WHERE task_collaborators.collaborator = users.id) AS email');
            }])->find($task->id);

            $reviewer = $collaboratorFlag->collaborators->where('flag', '1');
            $assignee = $collaboratorFlag->collaborators->where('flag', '0');

            $assignees = $this->_taskCollaboratorUpdate($task->id, $request->assignees ?? []); //updates assignees of the task
            $reviewers = $this->_taskCollaboratorUpdate($task->id, $request->reviewers ?? [], '1'); // updates reviewer for the task

            $newAssignees = $newReviewers = [];
            if ($assignees) {
                $newAssignees = json_decode($assignees->getContent())->collaborators;
            }
            if ($reviewers) {
                $newReviewers = json_decode($reviewers->getContent())->collaborators;
            }

            if (isset($request->task_type) && count($request->task_type)) {
                $taskTypeData = collect($request->task_type)->mapWithKeys(function ($taskType) use ($user) {
                    return [$taskType => [
                        'createdBy' =>  $user->id,
                        'updatedBy' =>  $user->id,
                    ]];
                });
                $task->taskType()->sync($taskTypeData);
            }
            DB::commit();

            $token = env('SLACK_API_TOKEN');

            if (count($newAssignees) || count($newReviewers)) {
                $collaborators = User::whereIn('id', array_merge($newAssignees, $newReviewers))->get();

                $newAssignees = $collaborators->whereIn('id', $newAssignees)->pluck('email')->toArray();
                $newReviewers = $collaborators->whereIn('id', $newReviewers)->pluck('email')->toArray();
                $creator = \Auth::user()->email;

                $token = config('app.bot_token');

                try {
                    if ($newAssignees) {

                        $aChannels = $collaborators->pluck('slack_username')->toArray();
                        $assigneeMessage = app(TaskService::class)->generateNewTaskBlock($task);

                        foreach ($aChannels as $aChannel) {
                            Http::withHeaders([
                                'Authorization' => "Bearer $token",
                            ])->post('https://slack.com/api/chat.postMessage', [
                                'channel' => '@' . $aChannel,
                                'blocks' => $assigneeMessage,
                                // 'text'  => "You have been assigned to a Task: $request->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                            ]);
                        }
                    }

                    if (count($newAssignees)) {
                        Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($assignee->toArray())], function ($message) use ($newAssignees, $request, $userName) {
                            $message->from(config('mail.from.address'), $userName);
                            $message->to($newAssignees);
                            $message->subject("New task `$request->title` Created.");
                        });
                    }
                } catch (Exception $e) {
                    return back()->with('error', 'Error while sending email.');
                }

                try {
                    if ($newReviewers) {

                        $rChannels = $collaborators->pluck('slack_username')->toArray();
                        $reviewerMessage = app(TaskService::class)->generateNewTaskBlock($task, 'YOU HAVE BEEN MARKED AS REVIEWER');

                        foreach ($rChannels as $rChannel) {
                            Http::withHeaders([
                                'Authorization' => "Bearer $token",
                            ])->post('https://slack.com/api/chat.postMessage', [
                                'channel' => '@' . $rChannel,
                                'blocks' => $reviewerMessage,
                                // 'text'  => "You Have been marked as Reviewer for Task: $request->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                            ]);
                        }
                    }

                    if (count($newReviewers)) {
                        Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($reviewer->toArray())], function ($message) use ($newReviewers, $request, $userName) {
                            $message->from(config('mail.from.address'), $userName);
                            $message->to($newReviewers);
                            $message->subject("New task `$request->title` Created.");
                        });
                    }
                } catch (Exception $e) {
                    return back()->with('error', 'Something Went Wrong!.');
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Something Went Wrong!.');
        }

        return back()->with('success', 'Task details updated successfully.');
    }

    public function destroy()
    {
        return 'coming soon';
    }

    public function updateTaskStatus(Request $request, $taskId)
    {
        return back();
        if ($request->status == null) {
            return back()->with('error', 'Something went wrong.');
        }
        $user = Auth::user();

        $task = Task::find($taskId);
        if (!$task) {
            return back()->with('error', 'Task not found.');
        }

        if ($request->status == ('0')) {
            $task->completedAt = null;
            $task->completedBy = 0;
        } elseif ($task->status != '2' && $request->status == '2') {
            $task->completedAt = Carbon::now();
            $task->completedBy = $user->id;
        }

        $task->status = $request->status;
        $task->updated_at = Carbon::now();
        $task->updatedBy = $user->id;
        $task->save();

        return back()->with('success', 'Task status updated successfully.');
    }

    public function usertaskList(Request $request, $id)
    {
        $pastDate = now()->subMonths(3)->startOfDay();
        $presentDate = now()->endOfDay();
        $routeFlag = false;

        $start_from = $request->star_from ? Carbon::parse($request->star_from) : $pastDate;
        $end_to = $request->end_to ? Carbon::parse($request->end_to) : $presentDate;
        $user = User::where('id', $id)->select('name', 'id')->first();
        $tasks = Task::selectRaw('tasks.*,
        (SELECT name FROM projects WHERE projects.id = tasks.project_id) AS projectName ,
        (SELECT name FROM users WHERE users.id = tasks.createdBy) AS createdBy,
        (SELECT name FROM users WHERE tasks.assignedBy = users.id LIMIT 1) AS assignedBy,
        (SELECT name FROM users WHERE tasks.completedBy = users.id LIMIT 1) AS completedBy,
        (SELECT GROUP_CONCAT(type) FROM taskTypes JOIN task_taskTypes_pivot ON taskTypes.id = task_taskTypes_pivot.taskTypes_id WHERE task_taskTypes_pivot.task_id = tasks.id) as types')
            ->with(['collaborators' => function ($query) {
                $query->selectRaw('task_collaborators.id, task_collaborators.collaborator,task_collaborators.taskId,task_collaborators.flag,
            (SELECT name FROM users WHERE task_collaborators.collaborator = users.id) AS collaborator');
            }, 'taskStatus:id,value,name'])
            ->whereHas('collaborators', function ($query) use ($id) {
                $query->where('collaborator', $id);
            })
            ->whereBetween('created_at', [$start_from, $end_to])
            ->orderBy('updated_at', 'DESC')
            ->get();
        // return $data;

        $projects = Project::select('id', 'name as group_name', 'name', 'sub_projects')->where('sub_projects', '0')->with('subprojects')->get();

        $task_types = TaskType::all();

        // return view('user.user_task', ['data' => $data, 'user' => trim($user[0])]);
        return Inertia::render('Tasks/index', compact('tasks', 'routeFlag', 'start_from', 'end_to', 'projects', 'task_types', 'user'));
    }

    public function uncompletedTask()
    {
        $tasks = Task::selectRaw('tasks.*,(SELECT name FROM users WHERE users.id = tasks.createdBy) AS createdBy,
        (SELECT name FROM users WHERE tasks.assignedBy = users.id LIMIT 1) AS assignedBy,
        (SELECT name FROM users WHERE tasks.completedBy = users.id LIMIT 1) AS completedBy,
        (SELECT GROUP_CONCAT(type) FROM taskTypes JOIN task_taskTypes_pivot ON taskTypes.id = task_taskTypes_pivot.taskTypes_id WHERE task_taskTypes_pivot.task_id = tasks.id) as types');

        $tasks = $tasks->with(['collaborators' => function ($query) {
            $query->selectRaw('task_collaborators.id, task_collaborators.collaborator,task_collaborators.taskId,task_collaborators.flag,
            (SELECT name FROM users WHERE task_collaborators.collaborator = users.id) AS collaborator');
        }])
            ->where('deadline', '<', now())
            ->orderBy('id', 'DESC')
            ->whereNotIn('status', ['5', '7', '8', '9', '10', '11'])
            ->get();

        // return view('task.uncompletedTask', compact('tasks'));
        return Inertia::render('Tasks/TaskPastDeadline', compact('tasks'));
    }

    public function getStagingUploadedTasks(Request $request)
    {
        $startFrom = $request->start_from
            ? Carbon::parse($request->start_from)->startOfDay()
            : now()->subMonths(3)->startOfDay();

        $endTo = $request->end_to
            ? Carbon::parse($request->end_to)->endOfDay()
            : now()->endOfDay();

        $tasks = Task::selectRaw('
                tasks.id, title, status, priority,
                (SELECT name FROM projects WHERE projects.id = tasks.project_id) AS projectName,
                (SELECT name FROM users WHERE users.id = tasks.createdBy) AS createdBy,
                (SELECT name FROM users WHERE users.id = tasks.assignedBy LIMIT 1) AS assignedBy,
                DATEDIFF(completedAt, assignedAt) AS completed,
                (
                    SELECT GROUP_CONCAT(type)
                    FROM taskTypes
                    JOIN task_taskTypes_pivot
                        ON taskTypes.id = task_taskTypes_pivot.taskTypes_id
                    WHERE task_taskTypes_pivot.task_id = tasks.id
                ) AS types
            ')
            ->where('status', '9') // Staging Uploaded
            ->whereBetween('created_at', [$startFrom, $endTo])
            ->whereHas('collaborators')
            ->with([
                'collaborators' => function ($query) {
                    $query->selectRaw('
                        task_collaborators.taskId,
                        task_collaborators.flag,
                        (SELECT name FROM users WHERE users.id = task_collaborators.collaborator) AS collaborator
                    ');
                },
                'taskStatus:id,value,name',
            ])
            ->orderByDesc('id');

        return Inertia::render('Tasks/StagingUploadTasks', [
            'tasks'      => fn () => $tasks->get(),
            'start_from' => fn () => $startFrom,
            'end_to'     => fn () => $endTo,
        ]);
    }
}

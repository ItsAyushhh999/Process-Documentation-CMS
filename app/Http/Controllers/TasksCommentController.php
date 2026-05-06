<?php

namespace App\Http\Controllers;

use App\Events\GenerateFeedEvent;
use App\Http\Services\TaskService;
use App\Jobs\SendCommentNotificationsJob;
use App\Models\Attachment;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskComment;
use App\Models\TaskStatus;
use App\Models\TaskStatusLog;
use App\Models\User;
use App\Services\FileHandler;
use App\Services\NotificationService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class TasksCommentController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
        // $this->notificationService = new NotificationService(new Logger());
        // $this->middleware('admin')->except(['index', 'show']);

    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // For task status
        $task = Task::find($request->taskId);
        $taskTitle = $task->title;
        $taskOldStatus = $task->status;

        if ($request->status == '0' || $request->status == '1') {
            $task->completedAt = null;
            $task->completedBy = 0;
        }

        if (!$task->assignedAt && $request->status == '0') {
            $task->assignedAt = Carbon::now();
            $task->assignedBy = $user->id;
        } elseif (!$task->completedAt && $request->status == '2') { //assigned for review status
            $task->completedAt = Carbon::now();
            $task->completedBy = $user->id;
        } elseif ($request->status == '5' && !$task->taskEndedAt) { // when task is ended/completed
            $task->taskEndedAt = Carbon::now();
            $task->taskEndedBy = $user->id;
        }

        if (isset($request->status)) {
            $task->status = (int) $request->status;
            $task->updatedBy = $user->id;
        }
        $task->updated_at = Carbon::now();
        $task->save();

        if (!$request->status || $taskOldStatus == $request->status) { //validation for comments only(when status is not changed or having null status from request)
            $this->validate($request, [
                'comment' => 'required|min:3',
                'attachments' => 'nullable|array|max:10',
                'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,csv,xls,xlsx,txt,docx',
            ]);
        }

        //task comment
        if (!($request->comment)) {
            return response()->json(['success'=> 'true', 'message' => 'Status updated.']);
        }

        $taskComment = new TaskComment();
        $taskComment->taskId = $request->taskId;
        $taskComment->comments = $request->comment;
        $taskComment->createdBy = $user->id;
        $taskComment->updatedBy = $user->id;
        $attachmentFlag = '1'; // 1: Comment attachment
        if ($request->comment_id) {
            $attachmentFlag = '2'; // 2: reply attachment
            $taskComment->reply_id = $request->comment_id;
        }

        $taskComment->save();
        
        if ($request->hasFile('attachments')) {
            $data = FileHandler::file_store($request->attachments);
            AttachmentController::store($data, $request->taskId, $attachmentFlag, $taskComment->id);
        }

        $attachments = Attachment::where('commentId', $taskComment->id)->get(['id', 'name', 'commentId']);

        broadcast(new \App\Events\TaskCommentEvent([
            'id'              => $taskComment->id,
            'taskId'          => $taskComment->taskId,
            'comments'        => $taskComment->comments,
            'reply_id'        => $taskComment->reply_id ?? 0,
            'created_by'      => Auth::id(),
            'user'            => Auth::user()->name,
            'profie_picture'  => Auth::user()->profile_picture ?? null,
            'created_at'      => now()->toDateTimeString(),
            'get_comment_image' => $attachments,
            'replies'         => [],
        ], $taskComment->taskId));
        
        dispatch(new SendCommentNotificationsJob(
                $task,
                $user,
                $request->comment,
                $taskComment->id,
                $taskTitle,
                $taskOldStatus,
            ));

        // return redirect()->back()->with('success', 'Comments has been added successfully.');
        return response()->json(['success' => true, 'message' => 'Comment added.']);
    }

    public function storeV2(Request $request)
    {
        // dd($request->file('attachments'));
        $user = Auth::user();

        // For task status
        $task = Task::find($request->taskId);
        $taskTitle = $task->title;
        $taskOldStatus = $task->status;

        if ($request->status == '0' || $request->status == '1') {
            $task->completedAt = null;
            $task->completedBy = 0;
        }

        if (!$task->assignedAt && $request->status == '0') {
            $task->assignedAt = Carbon::now();
            $task->assignedBy = $user->id;
        } elseif (!$task->completedAt && $request->status == '2') { //assigned for review status
            $task->completedAt = Carbon::now();
            $task->completedBy = $user->id;
        } elseif ($request->status == '5' && !$task->taskEndedAt) { // when task is ended/completed
            $task->taskEndedAt = Carbon::now();
            $task->taskEndedBy = $user->id;
        }

        if (isset($request->status)) {
            $task->status = (int) $request->status;
            $task->updatedBy = $user->id;
        }
        $task->updated_at = Carbon::now();
        $task->save();
        $statusLabels = [
            0 => 'Assigned',
            1 => 'In progress',
            2 => 'Assigned for review',
            3 => 'Reviewing',
            4 => 'Reviewed',
            5 => 'Completed',
            6 => 'Closed',
            7 => 'Created',
            8 => 'Staging - Ready to upload',
            9 => 'Staging - Upload completed',
            10 => 'Live - Ready to upload',
            11 => 'Live - Upload completed',
        ];

        // $selectedStatus = $statusLabels[$task->status];
        // $selectedoldStatus  = $statusLabels[$taskOldStatus];

        $selectedStatus = TaskStatus::where('value', $task->status)->value('name');
        $selectedoldStatus = TaskStatus::where('value', $taskOldStatus)->value('name');

        if ($taskOldStatus != $task->status) {
            $this->event('status', $task);
            $this->sendNotification($selectedStatus, $selectedoldStatus, $task, $user);
        }

        if (!$request->status || $taskOldStatus == $request->status) { //validation for comments only(when status is not changed or having null status from request)

            try {
                $this->validate($request, [
                    'comment' => 'required|min:3',
                    'attachments' => 'nullable|array|max:10',
                    'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,csv,xls,xlsx,xslx,txt,docx',
                ]);
            } catch (Exception $e) {
                // dd($e);

            }
        }

        //task comment
        if (!($request->comment)) {
            return response()->json(['status' => 'success', 'message' => 'Updated successfully.']);
        }

        $taskComment = new TaskComment();
        $taskComment->taskId = $request->taskId;
        $taskComment->comments = $request->comment;
        $taskComment->createdBy = $user->id;
        $taskComment->updatedBy = $user->id;
        $attachmentFlag = '1'; // 1: Comment attachment
        if ($request->comment_id) {
            $attachmentFlag = '2'; // 2: reply attachment
            $taskComment->reply_id = $request->comment_id;
        }

        $taskComment->save();

        broadcast(new \App\Events\TaskCommentEvent([
            'id'             => $taskComment->id,
            'taskId'         => $taskComment->taskId,
            'comments'       => $taskComment->comments,
            'reply_id'       => $taskComment->reply_id ?? 0,
            'created_by'     => Auth::id(),
            'user'           => Auth::user()->name,
            'profie_picture' => Auth::user()->profile_picture ?? null,
            'created_at'     => now()->toDateTimeString(),
            'getCommentImage'=> [],
            'replies'        => [],
            'isPinned'       => 0,
            'check'          => 0,
        ], (int) $taskComment->taskId));

        app(NotificationController::class)->create([
            'notification' => $request->comment,
            'taskId' => $task->id,
        ]);

        if ($request->hasFile('attachments')) {
            $data = FileHandler::file_store($request->attachments);
            AttachmentController::store($data, $request->taskId, $attachmentFlag, $taskComment->id);
        }

        $this->event('comment', $taskComment);
        $userIds = TaskCollaborator::join('tasks', 'task_collaborators.taskId', 'tasks.id')
            ->where('taskId', $request->taskId)
            ->get(['task_collaborators.collaborator', 'tasks.createdBy'])
            ->toArray();
        $userIds = collect($userIds)->flatten()->all();

        $users = User::whereIn('id', $userIds)->pluck('email')->toArray();

        $files = Attachment::where('commentId', $taskComment->id)->pluck('name');
        try {

            Mail::send(
                'emails.commentCreated',
                ['taskId' => $request->taskId, 'comment' => $request->comment, 'taskTitle' => $taskTitle],
                function ($message) use ($users, $user, $files) {
                    $userName = explode(' ', $user->name);
                    $userName = $userName[0] . ' ' . substr($userName[1], 0, 1);

                    $message->from(config('mail.from.address'), $userName);
                    $message->to($users);
                    $message->subject("$user->name added a Comment.");

                    foreach ($files as $file) {
                        $message->attach(public_path('storage/tasks/' . $file));
                    }
                }
            );
        } catch (Exception $e) {
            // dd($e->getMessage());
            // return back()->with('error', 'Error sending mail');
        }
        try {
            // dd($request, '1');
            $comment = strip_tags($request->comment);
            $channels = User::whereIn('id', $userIds)->pluck('slack_username')->toArray();
            $taskUrl = config('app.url') . "/tasks/{$task->id}/edit";
            $client = new Client();
            foreach ($channels as $channel) {
                try {
                    $response = $client->post('https://slack.com/api/chat.postMessage', [
                        'form_params' => [
                            'token' => config('app.bot_token'),
                            'blocks' => app(TaskService::class)->generateCommentBlock($user, $task, $comment, $taskUrl),
                            'channel' => '@' . $channel,
                        ],
                    ]);
                } catch (Exception $e) {
                    continue;
                }
            }

            // if ($response->getStatusCode() == 200) {
            //     if ($request->isXmlHttpRequest()) {
            //         return response()->json(['status' => 'success']);
            //     }

            //     return redirect()->back()->with('success', 'Request was successful');
            // } else {
            //     if ($request->isXmlHttpRequest()) {
            //         return response()->json(['status' => 'error', 'error' => 'an error occurred']);
            //     }

            //     return redirect()->back()->with('success', 'an error occurred');
            // }
        } catch (Exception $e) {
            return back()->with('error', 'Error sending message');
        }

        //return redirect()->back()->with('success', 'Comment saved.');
        response()->json(['status' => 'success', 'message' => 'Comment saved.']);
    }

    public function event($type, $task)
    {
        $userScope = Auth::user()->feed_scope;
        switch ($type) {
            case 'status':
                $taskStatusLogs = TaskStatusLog::selectRaw('
                        task_status_logs.*, "status" AS type,
                        (SELECT projects.name FROM projects WHERE projects.id = (
                            SELECT tasks.project_id FROM tasks WHERE tasks.id = task_status_logs.taskId
                        )) AS projectName,
                        (SELECT title FROM tasks WHERE tasks.id = task_status_logs.taskId) AS taskTitle,
                        (SELECT name FROM users WHERE task_status_logs.createdBy = users.id) AS createdByName,
                        (SELECT profile_picture FROM users WHERE users.id = task_status_logs.createdBy) AS profile_picture
                    ')
                    ->where('taskId', $task->id)
                    ->orderBy('created_at', 'DESC')
                    ->first()
                    ->toArray();

                event(new GenerateFeedEvent($taskStatusLogs, $task->id, $userScope));
                break;

            case 'comment':
                $commentQuery = TaskComment::selectRaw('
                    task_comments.id, task_comments.taskId, task_comments.createdBy, task_comments.comments, task_comments.created_at, "comment" AS type,
                    (SELECT projects.name FROM projects WHERE projects.id = (SELECT tasks.project_id FROM tasks WHERE tasks.id = task_comments.taskId)) AS projectName,
                    (SELECT title FROM tasks WHERE tasks.id = task_comments.taskId) AS taskTitle,
                    (SELECT name FROM users WHERE users.id = task_comments.createdBy) AS CreatorName,
                    (SELECT profile_picture FROM users WHERE users.id = task_comments.createdBy) AS profile_picture
                ')
                ->with([
                    'replies' => fn ($q) => $q->with('reply_creator')->orderBy('created_at', 'DESC'),
                ])
                ->orderBy('task_comments.created_at', 'DESC');

                if (isset($task->reply_id)) {
                    $commentQuery->where('task_comments.id', $task->reply_id);  // Fetch the parent comment if reply_id exists
                } else {
                    $commentQuery->where('task_comments.id', $task->id);  // Fetch the new comment
                }

                $comment = $commentQuery->first();

                if ($comment) {
                    // Remove <img> tags from the main comment text
                    $comment->comments = preg_replace('/<img[^>]*>/i', '', $comment->comments);
                    // Remove <img> tags directly on each reply's comment
                    foreach ($comment->replies as $reply) {
                        $reply->comments = preg_replace('/<img[^>]*>/i', '', $reply->comments);
                    }

                    // Prepare the minimal event data
                    $eventData = [
                        'commentId' => $comment->id,
                        'taskId' => $comment->taskId,
                        'createdBy' => $comment->createdBy,
                        'comments' => $comment->comments,
                        'created_at' => $comment->created_at,
                        'projectName' => $comment->projectName,
                        'taskTitle' => $comment->taskTitle,
                        'CreatorName' => $comment->CreatorName,
                        'type' => 'comment',
                        'profile_picture' => $comment->profile_picture,
                        'replies' => $comment->replies,
                    ];

                    // Broadcast the minimal and optimized event
                    event(new GenerateFeedEvent($eventData, $task->taskId, $userScope));
                }
                break;

            default:
                // Handle unknown or unsupported event type
                throw new InvalidArgumentException("Unknown event type: $type");
        }
    }

    /**
     * Function to pin/unpin a comment based on its ID.
     *
     * @param  mixed $id
     */
    public function pinComment($id)
    {

        // Find the comment by its ID
        $comment = TaskComment::find($id);

        // Check if the comment exists
        if (!$comment) {
            // If not found, redirect back with an error message
            return back()->with('error', 'Comment not found.');
        }

        // Toggle the pin status (0 to 1 or 1 to 0)
        $isPinned = $comment->isPinned == '0' ? '1' : '0';

        // Update the comment's pinned status and the user who pinned it
        $comment->isPinned = $isPinned;
        $comment->pinnedBy = $isPinned ? Auth::id() : 0;
        $comment->save();

        // Determine the message based on whether the comment is pinned or unpinned
        $message = $isPinned ? 'Pinned' : 'Unpinned';

        // Redirect back with a success message
        return back()->with('success', "$message successfully.");
    }

    public function sendNotification($selectedStatus, $selectedoldStatus, $task, $user)
    {
        $collaborator = TaskCollaborator::where('taskId', $task->id)->pluck('collaborator')->toArray();
        $token = config('app.bot_token');

        try {
            if ($collaborator) {

                $collaborator[] = $task->createdBy;
                $channels = User::whereIn('id', $collaborator)->pluck('slack_username')->unique()->toArray();
                $taskUrl = config('app.url') . "/tasks/{$task->id}/edit";
                // $data = "{$user->name} has changed the status of the task: <{$taskUrl}|*{$task->title}*> from a`{$selectedoldStatus}` to `{$selectedStatus}`.";
                $message = app(TaskService::class)->statusChangeMessage($task, $selectedStatus, $selectedoldStatus, $user->name);
                $client = new Client();
                foreach ($channels as $aChannel) {
                    $client->post('https://slack.com/api/chat.postMessage', [
                        'form_params' => [
                        'token' => config('app.bot_token'),
                        'channel' => '@' . $aChannel,
                        'blocks' => $message,
                        ],
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
        }
    }

    private function webNotification($task, $title, $message)
    {
        $collaborator = TaskCollaborator::where('taskId', $task->id)->pluck('collaborator')->toArray();

        $usersToNotify = User::whereIn('id', $collaborator)->pluck('email')->toArray();
        $url = config('app.url') . ('/tasks/' . $task->id . '/edit');

        $this->notificationService->notifyUser($usersToNotify, $title, $message, $url);
    }

    public function commentChecked($id)
    {
        TaskComment::where('id', $id)->update([
            'check' => DB::raw("CASE WHEN `check` = '0' THEN '1' ELSE '0' END"),
            'checkedBy'=> Auth::user()->id,
        ]);

        return back()->with('success', 'Check status updated successfully.');

    }

    public function notifyCicdAlert(Request $request, $pull_request_id, $branch_name)
    {
        try {

            if ($request->apiToken != 'I1LO1beNg') {
                return 'token failed';
            }

            $user = User::where('id', 26)->first();

            // $branch_name = $request->branch_name;
            // $pull_request_id = $request->pull_request_id;

            $task = \App\Models\GithubWebhook::where('repository_full_name', $pull_request_id)/* ->pluck('task_id') */ ->first();

            if (!$task) {
                \App\Models\GithubWebhook::create([
                    'status'    => 'UN-ATTACHED',
                    'pull_request_comment' => json_encode($request),
                ]);

                return response()->json([
                    'code'    => 404,
                    'status'  => 'error',
                    'message' => 'error',
                ]);
            }

            $taskId = $task->task_id;
            $repository_name = $task->repository_name;

            $deployType = 16;
            if (in_array($branch_name, ['production', 'main'])) {
                $deployType = 11;
            } elseif ($branch_name == 'staging') {
                $deployType = 9;
            }

            $selectedStatus = TaskStatus::where('value', $deployType)->value('name');

            $task = Task::find($taskId);
            $taskTitle = $task->title;
            $task->status = $deployType;
            $task->updatedBy = $user->id;
            $task->save();

            $message = "<a target=\"_blank\" href=\"https://us-west-1.console.aws.amazon.com/codesuite/codepipeline/pipelines/{$request->pipeline}/executions/{$request->execution}/visualization?region=us-west-1\">Uploaded to {$selectedStatus} ({$repository_name}) / {$request->execution}</a>";

            $taskComment = new TaskComment();
            $taskComment->taskId = $task->id;
            $taskComment->comments = $message;
            $taskComment->createdBy = $user->id;
            $taskComment->updatedBy = $user->id;
            $taskComment->save();

            $userIds = TaskCollaborator::join('tasks', 'task_collaborators.taskId', 'tasks.id')
                ->where('taskId', $task->id)
                ->get(['task_collaborators.collaborator', 'tasks.createdBy'])
                ->toArray();
            $userIds = collect($userIds)->flatten()->all();

            $users = User::whereIn('id', $userIds)->pluck('email')->toArray();

            // $files = Attachment::where('commentId', $taskComment->id)->pluck('name');
            try {

                Mail::send(
                    'emails.commentCreated',
                    ['taskId' => $task->id, 'comment' => $message, 'taskTitle' => $taskTitle],
                    function ($message) use ($users, $user) {
                        $userName = explode(' ', $user->name);
                        $userName = $userName[0] . ' ' . substr($userName[1], 0, 1);

                        $message->from(config('mail.from.address'), $userName);
                        $message->to($users);
                        $message->subject("$user->name added a Comment.");

                        // foreach ($files as $file) {
                        //     $message->attach(public_path('storage/tasks/' . $file));
                        // }
                    }
                );
            } catch (Exception $e) {
            }

            try {
                // dd($request, '1');
                $comment = $message;
                $channels = User::whereIn('id', $userIds)->pluck('slack_username')->toArray();
                $taskUrl = config('app.url') . "/tasks/{$task->id}/edit";
                $client = new Client();
                foreach ($channels as $channel) {
                    try {
                        $response = $client->post('https://slack.com/api/chat.postMessage', [
                            'form_params' => [
                                'token' => config('app.bot_token'),
                                'blocks' => app(TaskService::class)->generateCommentBlock($user, $task, $comment, $taskUrl),
                                'channel' => '@' . $channel,
                            ],
                        ]);
                    } catch (Exception $e) {
                        continue;
                    }
                }
            } catch (Exception $e) {
                //
            }

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => $task,
            ]);

        } catch (Exception $e) {
            \App\Models\GithubWebhook::create([
                'status'    => 'UN-ATTACHED',
                'pull_request_comment' => json_encode($e),
            ]);
        }

        return response()->json([
            'code'    => 404,
            'status'  => 'error',
            'message' => 'error',
        ]);
    }
}

<?php

namespace App\Jobs;

use App\Http\Controllers\NotificationController;
//use App\Http\Services\TaskService;
use App\Models\Attachment;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskStatus;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCommentNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Task $task,
        private User $user,
        private string $comment,
        private int $commentId,
        private string $taskTitle,
        private string $taskOldStatus
    ) {}

    public function handle(): void
    {
        $userIds = TaskCollaborator::join('tasks', 'task_collaborators.taskId', 'tasks.id')
            ->where('taskId', $this->task->id)
            ->get(['task_collaborators.collaborator', 'tasks.createdBy'])
            ->toArray();
        $userIds = collect($userIds)->flatten()->all();

        $users = User::whereIn('id', $userIds)->pluck('email')->toArray();
        $files = Attachment::where('commentId', $this->commentId)->pluck('name');
        $task  = $this->task;
        $user  = $this->user;

        if ($this->taskOldStatus != $task->status) {
            $selectedStatus    = TaskStatus::where('value', $task->status)->value('name');
            $selectedOldStatus = TaskStatus::where('value', $this->taskOldStatus)->value('name');

            // web notification
            $collaborators  = TaskCollaborator::where('taskId', $task->id)->pluck('collaborator')->toArray();
            $usersToNotify  = User::whereIn('id', $collaborators)->pluck('email')->toArray();
            $url            = config('app.url') . '/tasks/' . $task->id . '/edit';
            $statusMessage  = "{$user->name} has changed the status from `{$selectedOldStatus}` to `{$selectedStatus}`.";
            app(\App\Services\NotificationService::class)->notifyUser($usersToNotify, 'Task Status Updated', $statusMessage, $url);

            // slack status notification
            try {
                $channels   = User::whereIn('id', array_merge($collaborators, [$task->createdBy]))->pluck('slack_username')->unique()->toArray();
                $slackMsg   = app(\App\Http\Services\TaskService::class)->statusChangeMessage($task, $selectedStatus, $selectedOldStatus, $user->name);
                $client     = new Client();
                foreach ($channels as $channel) {
                    try {
                        $client->post('https://slack.com/api/chat.postMessage', [
                            'form_params' => [
                                'token'   => config('app.bot_token'),
                                'channel' => '@' . $channel,
                                'blocks'  => $slackMsg,
                            ],
                        ]);
                    } catch (Exception $e) {
                        continue;
                    }
                }
            } catch (Exception $e) {
                Log::error('Status slack failed: ' . $e->getMessage());
            }
        }

        app(NotificationController::class)->create([
            'notification' => $this->comment,
            'taskId'       => $task->id,
            'created_by'    => $this->user->id,
        ]);

        // Mail
        try {
            Mail::send(
                'emails.commentCreated',
                ['taskId' => $task->id, 'comment' => $this->comment, 'taskTitle' => $this->taskTitle],
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
            Log::error('Comment mail failed: ' . $e->getMessage());
        }

        // Slack comment notification
        try {
            $channels = User::whereIn('id', $userIds)->pluck('slack_username')->toArray();
            $taskUrl  = config('app.url') . "/tasks/{$task->id}/edit";
            $data     = app(\App\Http\Services\TaskService::class)->generateCommentBlock($user, $task, strip_tags($this->comment), $taskUrl);
            $client   = new Client();

            foreach ($channels as $channel) {
                try {
                    $client->post('https://slack.com/api/chat.postMessage', [
                        'form_params' => [
                            'token'   => config('app.bot_token'),
                            'blocks'  => $data,
                            'channel' => '@' . $channel,
                        ],
                    ]);
                } catch (Exception $e) {
                    continue;
                }
            }
        } catch (Exception $e) {
            Log::error('Comment slack failed: ' . $e->getMessage());
        }
    
        if ((int)$this->taskOldStatus !== (int)$this->task->status) {
            (new NotificationController())->create([
                'taskId'       => $this->task->id,
                'notification' => "Status changed to {$this->statusLabel($this->task->status)} from {$this->statusLabel($this->taskOldStatus)}",
                'created_by'   => $this->user->id,
            ]);
        }
    }
}
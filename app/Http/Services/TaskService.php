<?php

namespace App\Http\Services;

use App\Http\Traits\ApiTrait;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class TaskService
{
    use ApiTrait;
    private $notificationService;
    private $user;

    public function __construct(NotificationService $notificationServ)
    {
        $this->notificationService = $notificationServ;
        $this->user = Auth::guard('sanctum')->user();
    }

    public function createTask($request)
    {
        try {

            $assignees = $request->assignees ?? [];
            $reviwers = $request->reviewers ?? [];

            $status = count($assignees) ? '0' : '7';

            $tasks = Task::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'project_id' => $request->input('project_id'),
                'priority' => $request->input('priority'),
                'deadline' => $request->input('deadline'),
                'status' => $status,
                'createdBy'   => $this->user->id,
                'updatedBy'   => $this->user->id,
                'assignedAt'  => count($assignees) ? Carbon::now() : null,
                'assignedBy'  => count($assignees) ? $this->user->id : 0,
                'vox_task_id' => $request['ticket_id'] ?? null,
            ]);

            $tasks->TaskType()->sync($request->input('type'));
            foreach ($assignees as $assigee) {
                $this->taskCollaboratorCreate($assigee, $tasks->id);
            } //creates assignees for task

            foreach ($reviwers as $reviewer) {
                $this->taskCollaboratorCreate($reviewer, $tasks->id, '1');
            } //creates Reviwers for task

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'failed to create task' . $e->getMessage(),
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Task cretaed successfully',
            'result' => $tasks,
        ];
    }

    public function taskCollaboratorCreate($collaborator, $taskId, $flag = '0')
    {
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
                'createdBy'    => $this->user->id,
                'updatedBy'    => $this->user->id,
            ]
        );

        if ($TaskCollaborator->wasRecentlyCreated) {
            return response(['collaborator' => $TaskCollaborator->collaborator]);
        }
    }

    public function slackEmailAndMessage($task)
    {
        $collaboratorFlag = Task::with(['collaborators' => function ($query) {
            $query->selectRaw('task_collaborators.id, task_collaborators.collaborator,task_collaborators.taskId,task_collaborators.flag,
            (SELECT name FROM users WHERE task_collaborators.collaborator = users.id) AS collaborator,
            (SELECT email FROM users WHERE task_collaborators.collaborator = users.id) AS email');
        }])->find($task->id);

        $reviewer = $collaboratorFlag->collaborators->where('flag', '1');
        $assignee = $collaboratorFlag->collaborators->where('flag', '0');

        $creatorEmail = \Auth::user()->email ?? $this->user->email;
        $creatorSlackCode = \Auth::user()->slack_username ?? $this->user->slack_username;

        $assigneUsers = $assignee->pluck('email')->toArray();
        $reviewerUsers = $reviewer->pluck('email')->toArray();
        $usersToNotify = array_merge($assigneUsers, $reviewerUsers);
        $usersToNotify = array_unique($usersToNotify);
        $message = "You have been assigned to a new task: $task->title";
        $title = 'New Task Created';
        $userName = $this->user->name;

        $this->notificationService->notifyUser($usersToNotify, $title, $message, config('app.url') . ('/tasks/' . $task->id . '/edit'));

        $token = config('app.bot_token');
        try {
            Http::withHeaders([
                'Authorization' => "Bearer $token",
            ])->post('https://slack.com/api/chat.postMessage', [
                'channel' => '@' . $creatorSlackCode,
                // 'text'  => "You Have been marked as Creator for this Task: $task->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                'blocks' => app(self::class)->generateNewTaskBlock($task, 'YOU HAVE CREATED A TASK'),
            ]);

            Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => ['2']], function ($message) use ($creatorEmail, $task, $userName) {
                $message->from(config('mail.mailers.smtp.username'), $userName);
                $message->to($creatorEmail);
                $message->subject("New task `$task->title` Created.");
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return response()->json([
                'ss' => $e->getMessage(),
            ]);
            // dd($e->getMessage());
            // return back()->with('error', 'Error sending mail');
        }

        try {

            $rChannels = User::whereIn('email', $reviewer->pluck('email')->toArray())->whereNotNull('slack_username')->get();

            foreach ($rChannels as $rChannel) {
                Http::withHeaders([
                    'Authorization' => "Bearer $token",
                ])->post('https://slack.com/api/chat.postMessage', [
                    'channel' => '@' . $rChannel->slack_username,
                    // 'text'  => "You Have been marked as Reviewer for Task: $task->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                    'blocks' => $this->generateNewTaskBlock($task, 'YOU HAVE BEEN MARKED AS REVIEWER'),
                ]);
            }

            Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($reviewer->toArray())], function ($message) use ($reviewer, $task, $userName) {
                $message->from(config('mail.mailers.smtp.username'), $userName);
                $message->to($reviewer->pluck('email')->toArray());
                $message->subject("New task `$task->title` Created.");
            });
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return back()->with('error', $e->getMessage());
        }

        try {
            if (count($assignee) > 0) {
                $aChannels = User::whereIn('email', $assignee->pluck('email')->toArray())->whereNotNull('slack_username')->get();

                foreach ($aChannels as $aChannel) {
                    Http::withHeaders([
                        'Authorization' => "Bearer $token",
                    ])->post('https://slack.com/api/chat.postMessage', [
                        'channel' => '@' . $aChannel->slack_username,
                        // 'text'  => "You have been assigned to a Task: ($task->title). More details at => https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                        'blocks' => $this->generateNewTaskBlock($task),
                    ]);
                }

                Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($assignee->toArray())], function ($message) use ($assignee, $task, $userName) {
                    $message->from(config('mail.mailers.smtp.username'), $userName);
                    $message->to($assignee->pluck('email')->toArray());
                    $message->subject("New task `$task->title` Created.");
                });
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function generateCommentBlock($user, $task, $comment, $taskUrl)
    {
        $userName = $user?->name ?? 'Bot';
        $message = [
            [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => ":speech_balloon: $userName added a comment.",
                    'emoji' => true,
                ],
            ],
            [
                'type' => 'context',
                'elements' => [
                    [
                        'type' => 'image',
                        'image_url' => 'https://img.icons8.com/ios/50/000000/task.png',
                        'alt_text' => 'Task Icon',
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => '*Project:* ' . $task->project->name,
                    ],
                ],
            ],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*Task:* \n> _ $task->title _",
                ],
            ],
            [
                'type' => 'divider',
            ],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*Comment:* \n```$comment```",
                ],
            ],
            [
                'type' => 'divider',
            ],
            [
                'type' => 'actions',
                'elements' => [
                    [
                        'type' => 'button',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => 'View Task',
                            'emoji' => true,
                        ],
                        'value' => 'view_task',
                        'action_id' => 'button_view_task',
                        'url' => "$taskUrl",
                        'style' => 'primary',
                    ],
                ],
            ],
        ];

        return json_encode($message);
    }

    public function generateNewTaskBlock($task, $title = 'YOU HAVE BEEN ASSIGNED A TASK')
    {
        $priority = $task->priority == '0' ? 'Normal' : ($task->priority == '1' ? 'High' : 'Urgent');
        $deadline = isset($task->deadline) ? \Carbon\Carbon::parse($task->deadline)->format('m/d/Y h:i A') : 'Not Set';
        $taskTypes = $task->taskType->pluck('type')->implode(', ');
        // $description = strip_tags($task->description);
        $description = strip_tags(str_replace(['<br>', '<br/>', '<p>', '</p>'], ' ', $task->description));
        $collaborators = $task->getTaskCollaboratorDetail;
        $reviewers = $collaborators->where('flag', '1')->pluck('name')->implode(', ');
        $assignees = $collaborators->where('flag', '0')->pluck('name')->implode(', ');
        $project = $task->project->name;

        $message = [
            ['type' => 'divider'],
            [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => "$title ($project)",
                    'emoji' => true,
                ],
            ],
            ['type' => 'divider'],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*$task->title*",
                ],
            ],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "```$description```",
                ],
            ],
            [
                'type' => 'section',
                'fields' => [
                    ['type' => 'mrkdwn', 'text' => "*🎯 Priority:* `$priority`"],
                    ['type' => 'mrkdwn', 'text' => "*⏰ Deadline:* `$deadline`"],
                                        ['type' => 'mrkdwn', 'text' => "*🏷️ Type:* `$taskTypes`"],

                ],
            ],
                        [
                            'type' => 'section',
                            'text'=>[
                                'type' => 'mrkdwn',
                                'text' => "*🧑‍💼 Assignee(s):* `$assignees`",
                            ],
                            ],
                        [
                            'type' => 'section',
                            'text'=>[
                                'type' => 'mrkdwn',
                                'text' => "*🧑‍💼 Reviewer(s):* `$reviewers`",
                            ],
                            ],
            [
                'type' => 'actions',
                'elements' => [
                    [
                        'type' => 'button',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => 'View Task',
                            'emoji' => true,
                        ],
                        'style' => 'primary',
                        'url' => config('app.url') . '/tasks/' . urlencode($task->id) . '/edit',
                    ],
                ],
            ],
            ['type' => 'divider'],
        ];

        return json_encode($message);
    }

    public function statusChangeMessage($task, $newStatus, $oldStatus, $user)
    {
        $deadline = isset($task->deadline) ? \Carbon\Carbon::parse($task->deadline)->format('m/d/Y h:i A') : 'Not Set';
        $project = $task->project->name;

        $message = [
            ['type' => 'divider'],
            [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => "TASK STATUS HAS BEEN UPDATED ($project)",
                ],
            ],
            ['type' => 'divider'],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*$task->title*",
                ],
            ],
            [
                'type' => 'section',
                'fields' => [
                    ['type' => 'mrkdwn', 'text' => "*New Status:* `$newStatus`"],
                    ['type' => 'mrkdwn', 'text' => "*Previous Status:* `$oldStatus`"],
                    ['type' => 'mrkdwn', 'text' => "*Updated By:* `$user`"],
                ],
            ],
            [
                'type' => 'section',
                'fields' => [
                    ['type' => 'mrkdwn', 'text' => "*⏰ Deadline:* `$deadline`"],
                ],
            ],
            [
                'type' => 'actions',
                'elements' => [
                    [
                        'type' => 'button',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => 'View Task',
                            'emoji' => true,
                        ],
                        'style' => 'primary',
                        'url' => config('app.url') . '/tasks/' . urlencode($task->id) . '/edit',
                    ],
                ],
            ],
            ['type' => 'divider'],
        ];

        return json_encode($message);
    }
}

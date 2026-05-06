<?php

namespace App\Http\Controllers;

use App\Constants\FeedConstant;
use App\Events\GenerateFeedEvent;
use App\Http\Services\TaskService;
use App\Http\Traits\ApiJsonResponseTrait;
use App\Models\Attachment;
use App\Models\CodePipeLineLog;
use App\Models\Feed;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskComment;
use App\Models\TaskStatus;
use App\Models\User;
use App\Services\FileHandler;
use Carbon\Carbon;
use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CronJobController extends Controller
{
    use ApiJsonResponseTrait;

    /**
     * fetch the comments of specific tasks replied from the mail.
     *
     * @return void
     */
    public function retrieveComments()
    {
        $client = \Webklex\IMAP\Facades\Client::account('default');
        $client->connect();         //Connect to the IMAP Server
        $folders = $client->getFolder('INBOX');

        $unseenMessages = $folders->query()->since(Carbon::now())
            ->unSeen()
            ->get();

        foreach ($unseenMessages as $unseenMessage) {
            $createdAt = (collect($unseenMessage->getAttributes()['date'])->values()->all()[1][0]);
            $sender = (collect($unseenMessage->getAttributes()['from'])->values()[1][0]->mail);

            $htmlBody = $unseenMessage->getHtmlBody() ?? null;
            $textBody = $unseenMessage->getTextBody() ?? null;

            preg_match('#(?<=TaskId:=:>)\d+#', $textBody, $taskId, PREG_UNMATCHED_AS_NULL);
            $taskId = $taskId[0] ?? null;
            $message = null;
            if ($htmlBody) {
                $body = new DOMDocument();
                libxml_use_internal_errors(true);
                $body->loadHTML($htmlBody);
                $messageBody = $body->firstElementChild->nodeValue;

                preg_match_all('#-->[\s]*\K[\S\s]+?(?=From:)|-->[\s]*\K[\S\s]+#', $messageBody, $message);

                $message = isset($message[0][0]) ? $message[0][0] : $message[0];

                if (!$message || !isset($message[0])) {
                    $message = $body->getElementsByTagName('div')[0]->nodeValue ?? null;

                    $message = explode('On', $messageBody)[0];
                    $message = str_replace('Â', '', $message);
                }
            } elseif ($textBody) {
                $message = $textBody;
            }

            $user = User::where('email', $sender)->first();
            if (!$user) {
                continue;
            }

            $task = Task::find($taskId);
            if (!$task) {
                continue;
            }

            $comment = TaskComment::firstOrCreate(
                [
                    'taskId'     => $taskId,
                    'comments'   => $message,
                    'createdBy'  => $user->id,
                    'created_at' => $createdAt,
                ]
            );

            if ($comment->wasRecentlyCreated) {

                $oAttachment = $unseenMessage->getAttachments();

                $oAttachment->each(function ($oAttachment) use ($taskId, $comment, $user) {
                    $fileName = time() . '_' . $oAttachment->getAttributes()['name'];
                    $oAttachment->save(public_path('storage/tasks/'), $fileName);
                    Attachment::create([
                        'task_id'   => $taskId,
                        'name'      => $fileName,
                        'createdBy' => $user->id,
                        'updatedBy' => $user->id,
                        'flag'      => '1',
                        'commentId' => $comment->id,
                        'dir'       => 'null',
                    ]);
                });

                try {
                    Mail::send(
                        'emails.commentCreated',
                        ['taskId' => $taskId, 'comment' => $message],
                        function ($message) use ($user) {
                            $message->from(config('mail.mailers.smtp.username'), config('mail.from.name'));
                            $message->to('danshwaraj.c@shikhartech.com');
                            $message->subject("$user->name added a Comment.");

                            // foreach ($files as $file) {
                            //     $message->attach(public_path('storage/app/public/tasks' . $file));
                            // }
                        }
                    );
                } catch (Exception $e) {
                    // dd($e->getMessage());
                    continue;
                }
            }

            if ($comment) {                             //Marks as read if comments are saved
                $unseenMessage->setFlag('SEEN');
                $client->expunge();
            }
        }

        return response('Comments fetched');
    }

    public function unreportedTasks()
    {
        return true;
        $tasks = Task::WhereDoesntHave('comments')->whereIn('status', ['0', '1'])->get();
        dd($tasks);
    }

    /**
     * archive all tasks that are completed more than last 15 days.
     *
     * @return void
     */
    public function archiveCompletedTasks()
    {
        Task::where([
            ['status', '5'],
            ['taskEndedAt', '<', Carbon::now()->subDays(15)->endOfDay()],
        ])
            ->update([
                'status' => '6',
            ]);

        return response('Tasks has been archived.');
    }

    public function createTask()
    {
        $userId = 3;
        $projectId = 2;
        $title = 'DANSH-TASKS => ' . Carbon::now()->toDayDateTimeString();
        $user = User::find($userId);
        $userName = explode(' ', $user->name);
        $userName = $userName[0] . ' ' . substr($userName[1], 0, 1);

        return $oldTasks = Task::where([
            ['project_id', $projectId],
            ['createdBy', $userId],
            ['priority', '2'],
            ['status', '0'],
            ['updatedBy', $userId],
            ['assignedBy', $userId],
        ])->whereNull('description')->with('comments.replies')->get();

        $task = Task::create([
            'title' => $title,
            'project_id' => $projectId,
            'priority' => '2',
            'deadline' => Carbon::now()->addHours(7),
            'createdBy' => $user->id,
            'updatedBy' => $user->id,
            'status' => '0',
            'assignedAt' => Carbon::now(),
            'assignedBy' => $user->id,
        ]);

        $task->taskType()->attach([5]);
        TaskCollaborator::firstOrCreate(
            [
                'taskId' => $task->id,
                'collaborator' => 3,
                'flag' => '0',
            ],
            [
                'taskId' => $task->id,
                'collaborator' => 3,
                'flag' => '0',
                'createdBy' => $user->id,
                'updatedBy' => $user->id,
            ]
        );

        TaskCollaborator::firstOrCreate(
            [
                'taskId' => $task->id,
                'collaborator' => 3,
                'flag' => '1',
            ],
            [
                'taskId' => $task->id,
                'collaborator' => 3,
                'flag' => '1',
                'createdBy' => $user->id,
                'updatedBy' => $user->id,
            ]
        );

        foreach ($oldTasks as $oldTask) {
            foreach ($oldTask->comments as $comment) {
                // Check if there are no replies or none of the replies contain "done"
                $hasRepliesWithoutDone = $comment->replies->isEmpty() || $comment->replies->every(function ($reply) {
                    return stripos($reply->comments, 'done') === false;
                });

                if ($comment->reply_id === 0 && $hasRepliesWithoutDone) {
                    $task->comments()->create([
                        'comments' => $comment->comments,
                        'createdBy' => $comment->createdBy,
                        'updatedBy' => $comment->updatedBy,
                    ]);
                }
            }
            $oldTask->priority = '0'; // Set the status to '1' (or the appropriate status code)
            $oldTask->save();
        }

        foreach ($oldTasks as $taskToDelete) {
            if ($taskToDelete->comments->isEmpty()) {
                $taskToDelete->delete();
            }
        }

        return 'done';
    }

    /**
     * notifies pending tasks to reviewer and assignees.
     *
     * @return void
     */
    public function notifyTasks()
    {
        return 'here';
        $tasks = Task::whereIn('status', ['0', '1', '2', '3']) // 0:assigned, 1:In Progress, 2:Assigned for review, 3:reviewing
            ->with('getTaskCollaboratorDetail')->get();

        $token = env('SLACK_API_TOKEN'); // slack apiToken
        foreach ($tasks as $task) {

            $collaborators = $task->getTaskCollaboratorDetail; // fetch collaborators username and email from users table
            $reviewers = $collaborators->where('flag', '1');
            $assignees = $collaborators->where('flag', '0');

            // reviewers part
            if (in_array($task->status, ['2', '3'])) {
                // dd($reviewers->pluck('slack_username'), $task);
                try {
                    $rChannels = $reviewers->pluck('slack_username');
                    foreach ($rChannels as $rChannel) { // notifies reviewers through slack
                        Http::withHeaders([
                            'Authorization' => "Bearer $token",
                        ])->post('https://slack.com/api/chat.postMessage', [
                            'channel' => '@' . $rChannel,
                            'text'  => "You Have been marked as Reviewer for Task: $task->title. More details at https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                        ]);
                    }

                    // notifies reviewers through email
                    Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($reviewers->toArray())], function ($message) use ($reviewers, $task) {
                        $message->from(config('mail.mailers.smtp.username'));
                        $message->to($reviewers->pluck('email')->toArray());
                        $message->subject("New task `$task->title` Created.");
                    });
                } catch (Exception $e) {
                    // continue;
                }
            } else {
                try {

                    // Assignee Part
                    $aChannels = $assignees->pluck('slack_username');

                    foreach ($aChannels as $aChannel) { // notifies assignees through slack
                        Http::withHeaders([
                            'Authorization' => "Bearer $token",
                        ])->post('https://slack.com/api/chat.postMessage', [
                            'channel' => '@' . $aChannel,
                            'text'  => "You have been assigned to a Task: ($task->title). More details at => https://tdms.shikhartech.com/tasks/{$task->id}/edit",
                        ]);
                    }

                    // notifies assignees through email
                    Mail::send('emails.taskCreated', ['task' => $task, 'collaboratorFlag' => array_values($assignees->toArray())], function ($message) use ($assignees, $task) {
                        $message->from(config('mail.mailers.smtp.username'));
                        $message->to($assignees->pluck('email')->toArray());
                        $message->subject("New task $task->title` Created.");
                    });
                } catch (Exception $e) {
                    // continue;
                }
            }
        }

        return $this->success('Users notified successfully.');
    }

    /**
     * CronJob for sending email and slack message for user[assigne] where their task had passed deadline and status [0, 1].
     */
    public function uncompletedTaskEmail()
    {
        //Selecting Task where status is [0, 1] which means [Assigned && InProgress]
        $tasks = Task::whereIn('status', ['0', '1'])
            ->has('getTaskCollaboratorDetail')
            ->with(['getTaskCollaboratorDetail' => function ($query) {
                $query->where('flag', '0');
            }])
            ->where('deadline', '<', now())
            ->get();

        $taskList = [];
        foreach ($tasks as $task) {
            $collaborators = $task->getTaskCollaboratorDetail;
            $assignees = $collaborators->where('flag', 0);
            if ($assignees->isNotEmpty()) {
                foreach ($assignees as $assignee) {
                    $taskList[$assignee->email][] = $task;
                }
            }
        }

        $token = config('app.bot_token');
        foreach ($taskList as $email => $tasks) {
            // Sending Mail to Assignes if the task has passes its deadline
            try {
                Mail::send('emails.deadlinePastTask', ['tasks' => $tasks], function ($message) use ($email) {
                    $message->from(config('mail.mailers.smtp.username'));
                    $message->to($email);
                    $message->subject('Your assigned tasks have surpassed the due date. Here is the list of your tasks.');
                });
            } catch (Exception $e) {
                continue;
            }

            $taskDetails = '';    // Append each task link to $taskDetails

            foreach ($tasks as $task) {
                $taskUrl = "https://tdms.shikhartech.com/tasks/{$task->id}/edit";
                $taskDetails .= "\n . <{$taskUrl}|*{$task->title}*>";
            }

            $SlackUserName = User::where('email', $email)->value('slack_username');
            //Sending Slack Message to Assignes if the task has passes its deadline
            try {
                Http::withHeaders([
                    'Authorization' => "Bearer $token",
                ])->post('https://slack.com/api/chat.postMessage', [
                    'channel' => '@' . $SlackUserName,
                    'text'  => 'Your assigned tasks have surpassed the due date. Here is the list of your tasks: ' . $taskDetails,
                ]);
            } catch (Exception $e) {

            }
        }

        return back()->with('success', 'Email sent successfully.');
    }

    //sync
    public function mergeBranches()
    {
        ini_set('max_execution_time', '20000');
        ini_set('memory_limit', -1);

        $accessToken = config('github.accessToken');

        $client = new Client([
            'base_uri' => 'https://api.github.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ],
        ]);

        $response = $client->get('orgs/shikhartech/repos?per_page=100');

        $getContents = $response->getBody()->getContents();

        $repositories = json_decode($getContents);

        $repositories = collect($repositories)->whereNotIn('name', ['cultureshock-mobile-app', 'sunrun-aws-v2', 'shikhartech-site']);

        foreach ($repositories as $key => $repo) {

            if ($repo->archived) {
                continue;
            }

            $allBranches = $client->get("repos/shikhartech/$repo->name/branches");
            $getContents = $allBranches->getBody()->getContents();
            $branches = collect(json_decode($getContents));

            // $branchesNeedToBeDeleted = $branches->whereNotIn('name', ['production', 'main', 'staging', 'development']);

            // foreach ($branchesNeedToBeDeleted as $branch) {

            //     if (!$branch->protected) {
            //         continue;
            //     }

            //     $client->delete("/repos/shikhartech/$repo->name/branches/$branch->name/protection");
            // }

            // foreach ($branchesNeedToBeDeleted as $branch) {
            //     $client->delete("https://api.github.com/repos/shikhartech/$repo->name/git/refs/heads/{$branch->name}");
            // }

            foreach ($branches->whereIn('name', ['production', 'main', 'staging', 'development']) as $branch) {

                $response = $client->put("repos/shikhartech/$repo->name/branches/$branch->name/protection", [
                   'json' => [
                       'required_status_checks' => [
                           'strict' => true,
                           'contexts' => [],
                       ],
                       'enforce_admins' => true,
                       'required_pull_request_reviews' => [
                           'dismiss_stale_reviews' => false,
                           'require_code_owner_reviews' => false,
                           'required_approving_review_count' => in_array($branch->name, ['production', 'staging']) ? 1 : 0,
                           'require_last_push_approval' => false,
                       ],
                       'required_pull_request_reviews' => null,
                       'restrictions' => null,
                       'required_linear_history' => false,
                       'allow_force_pushes' => false,
                       'allow_deletions' => false,
                       'block_creations' => false,
                       'required_conversation_resolution' => true,
                       'lock_branch' => false,
                       'allow_fork_syncing' => true,
                   ],
                ]);

                if ($branch->name == 'main') {
                    try {
                        $client->post("repos/shikhartech/$repo->name/merges", [
                                'json' => [
                                'base' => 'main',
                                'head' => 'production',
                                ],
                            ]);
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                $response = $client->put("repos/shikhartech/$repo->name/branches/$branch->name/protection", [
                    'json' => [
                        'required_status_checks' => [
                            'strict' => true,
                            'contexts' => [],
                        ],
                        'enforce_admins' => true,
                        'required_pull_request_reviews' => [
                            'dismiss_stale_reviews' => false,
                            'require_code_owner_reviews' => false,
                            'required_approving_review_count' => in_array($branch->name, ['production', 'main', 'staging']) ? 1 : 0,
                            'require_last_push_approval' => false,
                        ],
                        'restrictions' => null,
                        'required_linear_history' => false,
                        'allow_force_pushes' => false,
                        'allow_deletions' => false,
                        'block_creations' => false,
                        'required_conversation_resolution' => true,
                        'lock_branch' => false,
                        'allow_fork_syncing' => true,
                    ],
                ]);
                $getContents = $response->getBody()->getContents();
                $branches = json_decode($getContents);
            }
        }

        foreach ($repositories as $repository) {

            try {
                // $response = $client->post("repos/shikhartech/$repository->name/merges", [
                //     'json' => [
                //         'base' => 'main',
                //         'head' => 'production',
                //     ],
                // ]);

                $response = $client->post("repos/imdanshraaj/$repository->name/merge-upstream", [
                    'json' => [
                        'branch' => 'main',
                    ],
                ]);

                $response = $client->post("repos/imdanshraaj/$repository->name/merge-upstream", [
                    'json' => [
                        'branch' => 'production',
                    ],
                ]);

            } catch (\Exception $e) {
                continue;
            }
        }

        foreach ($repositories as $repository) {

            try {

                $response = $client->post("repos/imdanshraaj/$repository->name/merge-upstream", [
                    'json' => [
                        'branch' => 'main',
                    ],
                ]);
            } catch (\Exception $e) {
                continue;
            }
        }

        foreach ($repositories as $repository) {

            try {
                $response = $client->post("repos/imdanshraaj/$repository->name/merge-upstream", [
                    'json' => [
                        'branch' => 'production',
                    ],
                ]);
            } catch (\Exception $e) {
                continue;
            }
        }

        foreach ($repositories as $repository) {

            try {
                $response = $client->post("repos/imdanshraaj/$repository->name/merge-upstream", [
                    'json' => [
                        'branch' => 'staging',
                    ],
                ]);
            } catch (\Exception $e) {
                continue;
            }
        }

        $response = $client->get('users/imdanshraaj/repos?per_page=100');

        $getContents = $response->getBody()->getContents();

        $repositories = json_decode($getContents);

        foreach ($repositories as $repository) {

            try {

                $response = $client->post("repos/imdanshraaj/$repository->name/merge-upstream", [
                    'json' => [
                        'branch' => $repository->default_branch,
                    ],
                ]);
            } catch (\Exception $e) {
                continue;
            }
        }

        return 'Done';
    }

    public function configure()
    {
        Artisan::call('config:clear');
        Artisan::call('migrate');

        return $this->success('Succeed.');
    }

    public function deployStatus(): string
    {
        try {
            $logs = CodePipeLineLog::where('is_notified', false)->get();
            $logsIds = [];
            foreach ($logs as $log) {
                $data = [];
                $data['created_by'] = $log->updated_by;
                $data['updated_by'] = $log->updated_by;
                $data['task_id'] = $log->task_id;
                $data['project_id'] = $log->project_id;
                $data['type'] = FeedConstant::FEED_TYPE_DEPLOYMENT;
                $data['source'] = FeedConstant::FEED_SOURCE_AWS;
                $data['status'] = FeedConstant::FEED_STATUS_SUCCESS;
                $dynamicStatus = 'Succeeded';

                $parentProjectId = $log->task->project->sub_projects;
                $pipelineId = null;

                switch (true) {
                    case $log->stage_name == 'production':
                        $pipelineId = $log->project->production_Pipeline;
                        break;
                    case $log->stage_name == 'staging':
                        $pipelineId = $log->project->staging_pipeline;
                        break;
                    case $log->stage_name == 'development':
                        $pipelineId = $log->project->development_pipeline;
                        break;
                    default:
                }

                $pipelineDetails = DeployController::initializeCodePipelineClient($parentProjectId);
                $pipelineStages = $pipelineDetails->getPipelineState([
                    'name' => $pipelineId,
                ]);

                $stages = collect($pipelineStages->get('stageStates'));
                $firstPipelineExecutionId = $stages->pluck('latestExecution.pipelineExecutionId')->first();
                $lastPipelineExecutionId = $stages->pluck('latestExecution.pipelineExecutionId')->last();
                $lastPipelineExecutionStatus = $stages->pluck('latestExecution.status')->last();

                $data['description'] = $stages->last()['actionStates'][0]['latestExecution']['summary'];

                if ($firstPipelineExecutionId != $lastPipelineExecutionId || $lastPipelineExecutionStatus != 'Succeeded') {
                    foreach ($stages as $stage) {
                        $data['description'] = $stage['actionStates'][0]['latestExecution']['summary'];
                        $dynamicStatus = $stage['latestExecution']['status'];

                        if (array_key_exists($stage['latestExecution']['status'], FeedConstant::$InProgress)) {
                            $data['status'] = FeedConstant::FEED_STATUS_PROCESS;
                            break;
                        } elseif (array_key_exists($stage['latestExecution']['status'], FeedConstant::$Failed)) {
                            $data['status'] = FeedConstant::FEED_STATUS_FAILURE;
                            break;
                        }
                    }
                } else {
                    $taskComment = $this->notifiedTask($log);
                    if ($taskComment) {
                        try {
                            $this->gitWebhooksService->notificationTask($taskComment, null);
                        } catch (Exception $exception) {
                            Log::error($exception->getMessage());
                        }
                    }
                }

                $data['title'] = sprintf(
                    '%s process for deploying %s on %s to the %s environment of the %s project.',
                    $log->updatedBy?->name,
                    $dynamicStatus,
                    $log->task->title,
                    $log->stage_name,
                    $log->task->project->name
                );

                if ($dynamicStatus != 'InProgress') {
                    $feed = Feed::create($data)->load(['createdBy:id,name', 'task:id,title', 'project:id,name'])->toArray();

                    event(new GenerateFeedEvent($feed));
                    $logsIds[] = $log->id;
                }
            }

            if (!empty($logsIds)) {
                CodePipeLineLog::whereIn('id', $logsIds)->update(['is_notified' => true]);
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }

        return 'OK';
    }

    /**
     * @param CodePipeLineLog $log
     * @return TaskComment|void
     */
    public function notifiedTask(CodePipeLineLog $log)
    {
        try {
            $taskStatus = null;

            switch (true) {
                case $log->stage_name == 'production':
                    $taskStatus = 11;
                    break;
                case $log->stage_name == 'staging':
                    $taskStatus = 9;
                    break;
            }

            $log->task->status = $taskStatus;
            $log->task->updatedBy = $log->updated_by;
            $log->task->save();

            $taskComment = new TaskComment();
            $taskComment->taskId = $log->task->id;
            $taskComment->comments = $log->task->title . ' Deployed on ' . $log->stage_name . ' By ' . $log->updatedBy->name;
            $taskComment->save();

            return $taskComment;
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function mergePrs($projectId, $prNumber)
    {
        try {
            $githubUserName = User::where('id', \Auth::user()->id)->pluck('github_username')->first();

            if (!$githubUserName) {
                return 'your github userName is not saved in TDMS.';
            }

            $accessToken = config('github.accessToken');

            $client = new Client([
                'base_uri' => 'https://api.github.com/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/vnd.github+json',
                    'X-GitHub-Api-Version' => '2022-11-28',
                ],
            ]);

            $repo = \App\Models\Project::where('id', $projectId)->pluck('github_repo_name')->first();

            $response = $client->get("repos/shikhartech/$repo/pulls/$prNumber");

            $getContents = $response->getBody()->getContents();

            $repository = json_decode($getContents);

            if (!$repository) {
                return 'please check repo name.';
            }

            $permissionId = 0;
            if ($repository->base->ref == 'production') {
                $permissionId = 3;
            } elseif ($repository->base->ref == 'staging') {
                $permissionId = 2;
            } elseif ($repository->base->ref == 'development') {
                $permissionId = 1;
            }

            $checkpermission = \App\Models\PermissionUser::where('user_id', \Auth::user()->id)->where('project_id', $projectId)->where('permission_id', $permissionId)->pluck('id')->first();

            if (!$checkpermission) {
                return 'No permission to upload.';
            }

            if ($repository->user->login != $githubUserName && !$checkpermission) {
                return 'You are not allowed to merge this branch.';
            }

            $response = $client->put("repos/shikhartech/$repo/pulls/$prNumber/merge", [
                'json' => [
                    'commit_title' => "merged from TDMS by $githubUserName",
                    'commit_message' => "merged from TDMS by $githubUserName",
                ],
            ]);

            $getContents = $response->getBody()->getContents();

            $repositories = json_decode($getContents);

            dd($repositories);
        } catch (Exception $e) {
            // dd($e->getMessage());
            return 'verify your url params';
        }
    }

    public function taskStatusComment()
    {
        $userId = 3;
        $user = User::where('id', $userId)->first();
        $taskIds = TaskCollaborator::where('collaborator', 3)->where('taskId', '>', 11593)->groupBy('taskId')->pluck('taskId')->toArray();

        $tasks = Task::whereIn('id', $taskIds)->where('id', '>', 11593)->whereIn('status', [0, 1])->get();

        foreach ($tasks as $task) {
            $assignees = TaskCollaborator::where('taskId', $task->id)->get();

            $assignee = $assignees->where('flag', 0);

            if ($assignee->count() == 1 && $assignee->pluck('collaborator')->first() == 3) {
                continue;
            }

            $comment = 'Do we have any updates on this?';

            /* if($task->deadline < '1day' && $task->deadline > now) {
                $comment = $comment.' we are approaching our task deadline.';
            }
            elseif($task->deadline > now)
            {
                $comment = $comment.' we crossed our task deadline.';
            } */

            $taskComment = new TaskComment();
            $taskComment->taskId = $task->id;
            $taskComment->comments = $comment;
            $taskComment->createdBy = $user->id;
            $taskComment->updatedBy = $user->id;
            $taskComment->save();

            $userIds = TaskCollaborator::join('tasks', 'task_collaborators.taskId', 'tasks.id')
            ->where('taskId', $task->id)
            ->get(['task_collaborators.collaborator', 'tasks.createdBy'])
            ->toArray();

            $userIds = collect($userIds)->flatten()->all();

            try {
                $comment = $taskComment->comments;
                $channels = User::whereIn('id', $userIds)->pluck('slack_username')->toArray();
                $taskUrl = config('app.url') . "/tasks/{$task->id}/edit";
                $client = new Client();
                foreach ($channels as $channel) {
                    $response = $client->post('https://slack.com/api/chat.postMessage', [
                        'form_params' => [
                            'token' => config('app.bot_token'),
                            'blocks' => app(TaskService::class)->generateCommentBlock($user, $task, $comment, $taskUrl),
                            'channel' => '@' . $channel,
                        ],
                    ]);
                }
            } catch (Exception $e) {
                //
            }
        }

        return 'Done';
    }

    public function taskToReview()
    {
        // status 2 is assigned for review
        $tasksToReview = Task::where('status', 2)
            ->with('getTaskCollaboratorDetail', function ($query) {
                $query->where('flag', '1');
            })
            ->get();
        $taskList = [];
        foreach ($tasksToReview as $task) {
            $collaborators = $task->getTaskCollaboratorDetail;
            $reviewers = $collaborators->where('flag', 1); // 1 is reviewer
            $link = config('app.url') . ('/tasks/' . $task->id . '/edit');
            $title = $task->title;
            foreach ($reviewers as $reviewer) {

                if (!isset($taskList[$reviewer->email])) {
                    $taskList[$reviewer->email] = [
                        'email' => $reviewer->email,
                        'slack_username' => $reviewer->slack_username,
                        'tasksList' => [],
                    ];
                }

                $taskList[$reviewer->email]['tasksList'][] = [
                    'title' => $title,
                    'link' => $link,
                ];
            }
        }

        $finalMailList = array_values($taskList);

        try {
            foreach ($finalMailList as $task) {

                // send mail
                $email = $task['email'];
                Mail::send('emails.tasksToReview', ['tasksList' => $task['tasksList']], function ($message) use ($email) {
                    $message->from(config('mail.mailers.smtp.username'));
                    $message->to($email);
                    $message->subject('Your assigned tasks to review.');
                });

                // send slack message
                $message = '<https://tdms.shikhartech.com|TDMS> - The following tasks are awaiting your review.';
                foreach ($task['tasksList'] as $taskItem) {
                    $message .= "\n <{$taskItem['link']}|{$taskItem['title']}>";
                }

                $client = new Client();

                // $client->post('https://slack.com/api/chat.postMessage', [
                //     'form_params' => [
                //         'token' => config('app.bot_token'),
                //         'channel' => '@' . $task['slack_username'],
                //         'text' => $message,
                //     ],
                // ]);
            }
        } catch (Exception $e) {
            // return $e->getMessage();
            Log::error($e->getMessage());
        }

        $tasksToReview = Task::where('status', 0)
            ->with('getTaskCollaboratorDetail', function ($query) {
                $query->where('flag', '0');
            })
            ->get();

        $taskList = [];
        foreach ($tasksToReview as $task) {
            $collaborators = $task->getTaskCollaboratorDetail;
            $reviewers = $collaborators->where('flag', 0); // 1 is reviewer
            $link = config('app.url') . ('/tasks/' . $task->id . '/edit');
            $title = $task->title;

            foreach ($reviewers as $reviewer) {

                if (!isset($taskList[$reviewer->email])) {
                    $taskList[$reviewer->email] = [
                        'email' => $reviewer->email,
                        'slack_username' => $reviewer->slack_username,
                        'tasksList' => [],
                    ];
                }

                $taskList[$reviewer->email]['tasksList'][] = [
                    'title' => $title,
                    'link' => $link,
                ];
            }
        }

        $finalMailList = array_values($taskList);
        try {
            foreach ($finalMailList as $task) {

                // send mail
                $email = $task['email'];
                Mail::send('emails.tasksToReview', ['tasksList' => $task['tasksList']], function ($message) use ($email) {
                    $message->from(config('mail.mailers.smtp.username'));
                    $message->to($email);
                    $message->subject('TDMS - Your Remaining assigned tasks.');
                });

                // send slack message
                $message = '<https://tdms.shikhartech.com|TDMS> - You have the following assigned tasks remaining.';
                foreach ($task['tasksList'] as $taskItem) {
                    $message .= "\n <{$taskItem['link']}|{$taskItem['title']}>";
                }

                $client = new Client();

                // $client->post('https://slack.com/api/chat.postMessage', [
                //     'form_params' => [
                //         'token' => config('app.bot_token'),
                //         'channel' => '@' . $task['slack_username'],
                //         'text' => $message,
                //     ],
                // ]);
            }
        } catch (Exception $e) {
            // return $e->getMessage();
            Log::error($e->getMessage());
        }

        $tasksToReview = Task::whereNotIn('status', ['6', '11', '5'])
            ->with('getTaskCollaboratorDetail')
            ->get();

        $taskList = [];
        foreach ($tasksToReview as $task) {
            $collaborators = $task->getTaskCollaboratorDetail;

            $reviewer = $collaborators->where('collaborator', 3)->first();
            if (!$reviewer) {
                continue;
            }

            $link = config('app.url') . ('/tasks/' . $task->id . '/edit');

            if (!isset($taskList[$reviewer->email])) {
                $taskList[$reviewer->email] = [
                    'email' => $reviewer->email,
                    'slack_username' => $reviewer->slack_username,
                    'tasksList' => [],
                ];
            }

            $taskList[$reviewer->email]['tasksList'][] = $link;
        }

        $finalMailList = array_values($taskList);

        try {
            foreach ($finalMailList as $task) {

                // send mail
                $email = $task['email'];
                Mail::send('emails.tasksToReview', ['tasksList' => $task['tasksList']], function ($message) use ($email) {
                    $message->from(config('mail.mailers.smtp.username'));
                    $message->to($email);
                    $message->subject('TDMS - List of remaining TASK TO UPLOADS.');
                });
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return 'Notified successfully.';
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
        $task->save();
        $selectedStatus = TaskStatus::where('value', $task->status)->value('name');
        $selectedoldStatus = TaskStatus::where('value', $taskOldStatus)->value('name');

        if ($taskOldStatus != $task->status) {
            $message = "{$user->name} has changed the status  from `{$selectedoldStatus}` to `{$selectedStatus}`.";
            $this->webNotification($task, 'Task Status Updated', $message);

            app(TasksCommentController::class)->sendNotification($selectedStatus, $selectedoldStatus, $task, $user);
        }

        if (!$request->status || $taskOldStatus == $request->status) { //validation for comments only(when status is not changed or having null status from request)
            $this->validate($request, [
                'comment' => 'required|min:3',
                'attachments' => 'nullable|array|max:10',
               'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,csv,xls,xlsx,txt',
            ]);
        }

        //task comment
        if (!($request->comment)) {
            return back()->with('success', 'Updated successfully.');
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

        $message = "{$user->name} has added a new comment.";
        $this->webNotification($task, 'New Comment Added', $message);

        app(NotificationController::class)->create([
            'notification' => $request->comment,
            'taskId' => $task->id,
        ]);

        if ($request->hasFile('attachments')) {
            $data = FileHandler::file_store($request->attachments);
            AttachmentController::store($data, $request->taskId, $attachmentFlag, $taskComment->id);
        }

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

                    $message->from(config('mail.mailers.smtp.username'), $userName);
                    $message->to($users);
                    $message->subject("$user->name added a Comment.");

                    foreach ($files as $file) {
                        $message->attach(public_path('storage/tasks/' . $file));
                    }
                }
            );
        } catch (Exception $e) {
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
                            // 'text' => $data,
                            'blocks' => app(TaskService::class)->generateCommentBlock($user, $task, $comment, $taskUrl),
                            'channel' => '@' . $channel,
                        ],
                    ]);
                } catch (Exception $e) {
                    continue;
                }
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error sending message');
        }

        return redirect()->back()->with('success', 'Comments has been added successfully.');

    }
}

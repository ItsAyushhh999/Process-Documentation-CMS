<?php

namespace App\Http\Services;

use App\Constants\FeedConstant;
use App\Events\GenerateFeedEvent;
use App\Http\Controllers\NotificationController;
use App\Models\Attachment;
use App\Models\Feed;
use App\Models\GithubWebhook;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskComment;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GitWebhooksService
{
    public static function makeDescription($pullRequest, $taskId)
    {
        $body = data_get($pullRequest, 'body');
        if (!$body) {
            return '';
        }

        $description = preg_replace(
            ["#^https://tdms.shikhartech.com/tasks/$taskId/edit#mi", "#^TID-$taskId\b#mi"],
            '',
            $body,
            count: $count
        );

        return $count > 1 ? $body : $description;
    }

    public function assignGitReview(Task $task, $pullRequest, $userId)
    {
        try {

            $collaborators = TaskCollaborator::select(
                'task_collaborators.*',
                'users.name as collaboratorName',
                'users.github_username'
            )
                ->where('taskId', $task->id)
                ->join('users', 'users.id', 'task_collaborators.collaborator')
                ->get();

            $flaggedCollaborators = $collaborators->where('flag', '1');

            // Extract reviewer GitHub usernames
            $reviewers = $flaggedCollaborators
                ->pluck('github_username')
                ->filter()
                ->values();

            // Extract reviewer display names
            $reviewerNames = $flaggedCollaborators
                ->pluck('collaboratorName')
                ->filter()
                ->values();

            $url = data_get($pullRequest, 'url') . '/requested_reviewers';

            $response = Http::withToken(config('github.accessToken'))
                ->withHeaders(['Accept' => 'application/vnd.github+json'])
                ->post($url, ['reviewers' => $reviewers->all()]);

            if ($response->successful()) {
                // Format reviewer names into natural sentence
                $formattedNames = match (count($reviewerNames)) {
                    0 => '',
                    1 => $reviewerNames[0],
                    2 => implode(' and ', $reviewerNames->all()),
                    default => implode(', ', $reviewerNames->slice(0, -1)->all()) . ', and ' . $reviewerNames->last(),
                };
                // Save comment
                $taskComment = new TaskComment();
                $taskComment->taskId = $task->id;
                $taskComment->createdBy = $userId;
                $taskComment->comments = sprintf('%s set as reviewer%s.', $formattedNames, count($reviewerNames) > 1 ? 's' : '');
                $taskComment->save();

                return $response->json();
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return null;
    }

    public function getReviewStatus($status): string
    {
        $styles = [
            'commented' => 'bg-blue-100 text-blue-800',
            'request_changes' => 'bg-orange-100 text-orange-800',
            'approved' => 'bg-green-100 text-green-800',
        ];

        $labels = [
            'commented' => 'Commented',
            'request_changes' => 'Changes Requested',
            'approved' => 'Approved',
        ];

        $style = $styles[$status] ?? 'bg-gray-100 text-gray-800';
        $label = $labels[$status] ?? ucfirst($status);

        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold ' . $style . '">' .
            htmlspecialchars($label) .
            '</span>';
    }

    public function updateTaskStatus(Task $task, string $status, string $baseBranch, string $gitUsername): ?Task
    {
        $user = User::where('github_username', $gitUsername)->first();

        if ($status !== 'approved') {
            return null;
        }

        $branchStatusMap = [
            'production' => '10',
            'staging'    => '8',
            'main'       => '10',
        ];

        if (isset($branchStatusMap[$baseBranch])) {
            $task->status = $branchStatusMap[$baseBranch];
            $task->updatedBy = $user?->id;
            $task->save();

            return $task;
        }

        return null;
    }

    /**
     * @param  mixed|int $type 0: pull requests, 1: comments
     *@throws \Exception
     *
     * Extract the useful information form webhooks response
     */
    public function webhooks($data = [], int $type = 0)
    {
        $task = null;
        $user = null;
        $githubUsername = null;

        try {
            $response = [];
            $pullRequest = data_get($data, 'pull_request') ?? data_get($data, 'issue');
            preg_match(
                '#(?<=https://tdms.shikhartech.com/tasks/)\d+(?=/edit)|(?<=TID-)\d+\b#i',
                $pullRequest['body'],
                $matches
            );

            if (count($matches) > 0) {
                $taskId = $matches[0];
                $task = Task::find($taskId);

                if ($task) {
                    $response['task_id'] = $taskId;
                    $response['status'] = 'ATTACHED';
                }
            }

            $htmlUrl = data_get($pullRequest, 'html_url');
            $title = data_get($pullRequest, 'title');
            $reviewUrl = data_get($data, 'comment.html_url');
            $reviewPath = data_get($data, 'comment.path');
            $action = data_get($data, 'action');
            $base = data_get($data, 'pull_request.base.repo.full_name');
            $ref = data_get($data, 'pull_request.base.ref');
            $base = $base ? " on $base ($ref)" : '';
            $comment = data_get($data, 'comment.body');
            $review = data_get($data, 'review');
            $reviewState = data_get($data, 'review.state');
            $baseBranch = data_get($pullRequest, 'base.ref');

            if ($review) {
                $comment = data_get($review, 'body');
                $reviewUrl = data_get($review, 'html_url');
                $githubUsername = data_get($review, 'user.login');
            }

            // update task status when pr approved i.e Ready to upload staging or Ready to upload Live according to PR.
            if ($task && $review) {
                $this->updateTaskStatus($task, $reviewState, $baseBranch, $githubUsername);
            }

            if (data_get($data, 'comment.user.login')) {
                $githubUsername = data_get($data, 'comment.user.login');
            }

            $body = sprintf(
                "%s \n \n PR%s:\n\n<span class=\"text-blue-500\">[%s](%s)</span>",
                $type ? $comment : self::makeDescription($pullRequest, $taskId ?? 0),
                $base,
                $title,
                $htmlUrl,
            );

            $comment = Str::markDown($body);

            if ($reviewUrl) {
                $reviewHtml = "
                  <br>
                   <div>
                    <span class='text-l font-bold text-red-500'>Please check!!</span>
                    <br>
                ";

                if ($reviewState) {
                    $badgeHtml = $this->getReviewStatus($reviewState); // returns the badge span
                    $reviewHtml .= '
                        <div class="p-4 border rounded-md bg-gray-50 space-y-2">
                            <p class="text-sm text-gray-700">
                                Review status has been updated:
                            </p>
                            ' . $badgeHtml . '
                        </div>';
                }

                if ($reviewPath) {
                    $reviewHtml .= "
                        <br>
                        <div class='flex items-center gap-2'>
                            <div class='font-bold'>File:</div>
                            <div class='text-red-500'>$reviewPath</div>
                        </div>
                   ";
                }

                $reviewHtml .=
                    <<<EOD
                    <br>
                    <div>
                        <a href="$reviewUrl" class="text-blue-500" target="_blank">$reviewUrl</a>
                    </div>
                </div>
                EOD;

                $comment = $comment . $reviewHtml;
            }

            $response['pull_request_id'] = $pullRequest['id'];
            $response['pull_request_url'] = $htmlUrl;
            $response['pull_request_title'] = data_get($pullRequest, 'title');
            $response['pull_request_sender_username'] = data_get($data, 'comment.user')['login'] ?? data_get($pullRequest, 'user')['login'];
            $response['pull_request_sender_url'] = data_get($pullRequest, 'user')['html_url'];
            $response['pull_request_comment'] = $comment;
            $response['repository_full_name'] = data_get($pullRequest, 'merge_commit_sha');

            if (isset($response['pull_request_sender_username'])) {
                if (data_get($pullRequest, 'merged')) {
                    $user = User::where('github_username', $pullRequest['merged_by']['login'])->first();
                } elseif ($githubUsername) {
                    $user = User::where('github_username', $githubUsername)->first();
                } else {
                    $user = User::where('github_username', $response['pull_request_sender_username'])->first();
                }

                $response['user_id'] = $user?->id;
            }

            $repository = data_get($data, 'repository');
            if ($repository) {
                $response['repository_name'] = data_get($repository, 'name');
                $response['repository_url'] = data_get($repository, 'html_url');
            }

            $sender = data_get($data, 'sender');
            if ($sender) {
                $response['sender_username'] = data_get($sender, 'login');
                $response['sender_url'] = data_get($sender, 'html_url');
            }

            if ($type == 1) { // 1: comment in pr
                $githubWebhook = GithubWebhook::create($response);
            } else {
                $githubWebhook = GithubWebhook::updateOrCreate(
                    ['pull_request_url' => $response['pull_request_url']],
                    $response
                );
            }

            if ($action == 'opened' && $taskId && $pullRequest && data_get($response, 'user_id')) {
                self::assignGitReview($task, $pullRequest, $response['user_id']);
            }

            self::prepareForFeeds($data, $task, $user);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        return $githubWebhook;
    }

    /**
     * @param string $markdown
     * @return string|null|void
     */
    public function markdownConverter(string $markdown = '')
    {
        try {
            $markdown = preg_replace('/### (.*)/', '<h3>$1</h3>', $markdown);
            $markdown = preg_replace('/- (.*)/', '<li>$1</li>', $markdown);
            $markdown = preg_replace('/(?:^|\n)(?!<\/li>)<li>(.*?)(?=\n|$)/s', '<ul>$1</ul>', $markdown);
            $markdown = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" class="text-blue-500" target="_blank">$1</a>', $markdown);
            $markdown = nl2br($markdown);

            return preg_replace('/!\[(.*?)\]\((.*?)\)/', '<img src="$2" alt="$1">', $markdown);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function commentTask(GithubWebhook $gitWebhook, ?User $user, $shouldPin = false): ?TaskComment
    {
        $task = $gitWebhook->task;

        if ($task && $gitWebhook->notified == 0) {
            $taskComment = new TaskComment();
            $taskComment->taskId = $task->id;
            $taskComment->comments = $gitWebhook->pull_request_comment;
            $taskComment->createdBy = $gitWebhook->user_id;
            //           $taskComment->updatedBy = $user->id;

            if ($shouldPin) {
                $taskComment->isPinned = '1';
                $taskComment->pinnedBy = $gitWebhook->user_id;
            }

            $taskComment->save();

            $gitWebhook->notified = $taskComment->id;
            $gitWebhook->save();

            return $taskComment;
        }

        return null;
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function notificationTask(TaskComment $taskComment, ?User $user): void
    {
        app(NotificationController::class)->create([
            'notification' => $taskComment->comments,
            'taskId' => $taskComment->taskId,
            //            'created_by' => $user->id
            'created_by' => 0,
        ]);

        $userIds = TaskCollaborator::join('tasks', 'task_collaborators.taskId', 'tasks.id')
            ->where('taskId', $taskComment->taskId)
            ->get(['task_collaborators.collaborator', 'tasks.createdBy'])
            ->toArray();

        $userIds = collect($userIds)->flatten()->all();

        $users = User::whereIn('id', $userIds)->pluck('email')->toArray();
        $files = Attachment::where('commentId', $taskComment->id)->pluck('name');

        try {
            Mail::send(
                'emails.commentCreated',
                ['taskId' => $taskComment->taskId, 'comment' => $taskComment->comments, 'taskTitle' => $taskComment->task->title],
                function ($message) use ($users, $user, $files) {
                    //                    $userName = explode(' ', $user->name);
                    //                    $userName = $userName[0] . ' ' . substr($userName[1], 0, 1);

                    $message->from(config('mail.mailers.smtp.username'), 'BOT');
                    $message->to($users);
                    //                    $message->subject("$user->name added a Comment.");
                    $message->subject('Bot added a Comment.');

                    foreach ($files as $file) {
                        $message->attach(public_path('storage/tasks/' . $file));
                    }
                }
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        try {

            $comment = strip_tags($taskComment->comments);
            $channels = User::whereIn('id', $userIds)->pluck('slack_username')->toArray();
            $taskUrl = config('app.url') . "/tasks/{$taskComment->taskId}/edit";

            $client = new Client();
            foreach ($channels as $channel) {
                $response = $client->post('https://slack.com/api/chat.postMessage', [
                    'form_params' => [
                        'token' => config('app.bot_token'),
                        // 'text' => $data,
                        'blocks' => app(TaskService::class)->generateCommentBlock($user, $taskComment->task, $comment, $taskUrl),
                        'channel' => '@' . $channel,
                    ],
                ]);
            }
            if ($response->getStatusCode() != 200) {
                throw new \Exception('Something went wrong.');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function prepareForFeeds($data, $task = null, $user = null): void
    {
        $feedData = [];
        $feedData['source'] = FeedConstant::FEED_SOURCE_GITHUB;
        $pullRequest = $data['pull_request'] ?? null;
        $feedData['description'] = data_get($pullRequest, 'body');
        $feedData['username'] = data_get($pullRequest, 'user.login');
        $feedData['type'] = FeedConstant::FEED_TYPE_PULL_REQUEST;

        if ($task) {
            $feedData['task_id'] = $task->id;
            $feedData['project_id'] = $task->project->id;
        }

        if ($user) {
            $feedData['created_by'] = $user->id;
            $feedData['updated_by'] = $user->id;
        }

        if ($data['action'] == 'opened') {
            $action = 'created';
            $feedData['status'] = FeedConstant::FEED_STATUS_CREATED;
        } elseif (data_get($pullRequest, 'merged')) {
            $action = 'merged';
            $feedData['status'] = FeedConstant::FEED_STATUS_MERGE;
            $feedData['username'] = data_get($pullRequest, 'merged_by')['login'];
        } elseif ($data['action'] == 'closed') {
            $action = 'closed';
            $feedData['status'] = FeedConstant::FEED_STATUS_CLOSE;
        } elseif (data_get($pullRequest, 'review')) {
            $action = 'reviewed';
            $feedData['status'] = FeedConstant::FEED_STATUS_REVIEWED;
            $feedData['username'] = data_get($pullRequest, 'review')['user']['login'];
            $feedData['description'] = data_get($pullRequest, 'review')['body'];
        } elseif ($data['action'] == 'synchronize') {
            $action = 'synchronized';
            $feedData['status'] = FeedConstant::FEED_STATUS_SYNCHRONIZED;
        } else {
            $action = $data['action'];
            $feedData['status'] = FeedConstant::FEED_STATUS_OTHERS;
        }

        $title = sprintf('%s %s a pull request on the %s.', ($user->name ?? $feedData['username']), $action, data_get($data['repository'], 'name'));

        if ($task) {
            $title = sprintf('%s %s a pull request on the %s for the task named %s within the %s .', ($user->name ?? $feedData['username']), $action, data_get($data['repository'], 'name'), $task->title, $task->project?->name);
        }

        $feedData['title'] = $title;
        $feed = Feed::create($feedData)->load(['createdBy:id,name', 'task:id,title', 'project:id,name'])->toArray();

        event(new GenerateFeedEvent($feed));
    }
}

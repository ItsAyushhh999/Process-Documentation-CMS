<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\Task;
use App\Models\TaskActivityLog;
use App\Models\TaskCollaborator;
use App\Models\TaskComment;
use App\Models\TaskStatusLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DailyFeedController extends Controller
{
    public $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::id();

            return $next($request);
        });
    }

    public function index()
    {
        return Inertia::render('DailyFeeds/index');
    }

    public function show()
    {
        $today = Carbon::today();

        // Get task IDs associated with the user
        $taskIds = TaskCollaborator::where('collaborator', $this->userId)
            ->orWhereHas('task', fn ($query) => $query->where('createdBy', $this->userId))
            ->distinct()
            ->pluck('taskId');

        // Fetch today's data for comments, task status logs, feeds, and activity logs
        $comments = $this->fetchComments($taskIds, $today);
        $taskStatusLogs = $this->fetchTaskStatusLogs($taskIds, $today);
        $feeds = $this->fetchFeeds($taskIds, $today);
        $taskActivityLogs = $this->fetchTaskActivityLogs($taskIds, $today);

        // Fetch task details and join with project table to get projectName
        $tasks = Task::selectRaw('tasks.id, tasks.description,tasks.title, tasks.created_at,tasks.status,
        (SELECT name FROM projects WHERE projects.id = tasks.project_id) AS projectName,
        (SELECT profile_picture FROM users WHERE users.id = tasks.createdBy) AS profilePicture,
        (SELECT name FROM users WHERE users.id = tasks.createdBy) AS creatorName')
            ->whereIn('tasks.id', $taskIds)
            ->get();

        // Combine all types into a single collection, grouped by taskId
        $groupedTasks = $tasks->map(function ($task) use ($comments, $taskStatusLogs, $feeds, $taskActivityLogs) {
            $taskComments = $comments->where('taskId', $task->id);
            $taskStatusLogs = $taskStatusLogs->where('taskId', $task->id);
            $taskFeeds = $feeds->where('task_id', $task->id);
            $taskActivityLogs = $taskActivityLogs->where('taskId', $task->id);

            // Merge all items into a single collection and sort by `created_at`
            $allActivities = collect()
                ->merge($taskComments)
                ->merge($taskStatusLogs)
                ->merge($taskFeeds)
                ->merge($taskActivityLogs)
                ->sortByDesc('created_at')
                ->values()
                ->map(function ($item) {
                    $item->humanReadableCreatedAt = Carbon::parse($item->created_at)->diffForHumans();

                    return $item;
                });

            // Find max created_at from all activities, if any
            $latestActivity = $allActivities->max('created_at');

            // Compare task's created_at with latest activity timestamp
            $latestCreatedAt = $latestActivity && $latestActivity > $task->created_at
                                ? $latestActivity
                                : $task->created_at;

            // Only include tasks that have at least one activity
            if ($allActivities->isNotEmpty() || in_array($task->status, [0, 1])) {
                return [
                    'taskId' => $task->id,
                    'projectName' => $task->projectName,
                    'taskDescription' => $task->description,
                    'taskTitle' => $task->title,
                    'humanReadableCreatedAt' => Carbon::parse($task->created_at)->diffForHumans(),
                    'taskStatus' => $task->status,
                    'profile_picture' => $task->profilePicurre,
                    'creatorName' => $task->creatorName,
                    'activity' => $allActivities,
                    'latestCreatedAt' => $latestCreatedAt, // Temporary key for sorting
                ];
            }

            return null;
        })->values();

        // Sort tasks by `latestCreatedAt` and remove it from the final output
        $sortedTasks = $groupedTasks->sortByDesc('latestCreatedAt')
            ->values()
            ->map(function ($task) {
                unset($task['latestCreatedAt']); // Remove the temporary key after sorting

                return $task;
            });

        return response()->json($sortedTasks);
    }

    private function fetchComments($taskIds, $today)
    {
        return TaskComment::with([
            'getCommentImage:name,commentId',
            'replies' => fn ($q) => $q->with('getReplyImage', 'reply_creator')->whereDate('created_at', $today)->orderBy('created_at', 'DESC'),
        ])
            ->join('users', 'task_comments.createdBy', '=', 'users.id')
            ->where(function ($query) use ($taskIds) {
                $query->whereIn('task_comments.taskId', $taskIds)
                    ->orWhere('task_comments.createdBy', $this->userId);
            })
            ->where('task_comments.reply_id', 0)
            ->whereDate('task_comments.created_at', $today)
            ->select([
                'task_comments.id',
                'task_comments.taskId',
                'task_comments.comments',
                'task_comments.created_at',
                'users.name as CreatorName',
                'users.profile_picture as profile_picture',
                \DB::raw('"comment" as type'),
            ])
            ->orderBy('task_comments.created_at', 'DESC')
            ->get();
    }

    private function fetchTaskStatusLogs($taskIds, $today)
    {
        return TaskStatusLog::join('users', 'task_status_logs.createdBy', '=', 'users.id')
            ->where(function ($query) use ($taskIds) {
                $query->whereIn('task_status_logs.taskId', $taskIds)
                    ->orWhere('task_status_logs.createdBy', $this->userId);
            })
            ->whereDate('task_status_logs.created_at', $today)
            ->select([
                'task_status_logs.*',
                'users.name as createdByName',
                'users.profile_picture as profile_picture',
                \DB::raw('"status" as type'),
            ])
            ->orderBy('task_status_logs.created_at', 'DESC')
            ->get();
    }

    private function fetchFeeds($taskIds, $today)
    {
        return Feed::with(['task:id,title,status,description', 'project:id,name'])
            ->whereIn('task_id', $taskIds)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    private function fetchTaskActivityLogs($taskIds, $today)
    {
        return TaskActivityLog::join('users as creator', 'task_activity_logs.createdBy', '=', 'creator.id')
            ->leftJoin('users as added', 'task_activity_logs.newId', '=', 'added.id')
            ->leftJoin('users as removed', 'task_activity_logs.oldId', '=', 'removed.id')
            ->leftJoin('taskTypes as addedTypes', 'task_activity_logs.newId', '=', 'addedTypes.id')
            ->leftJoin('taskTypes as removedTypes', 'task_activity_logs.oldId', '=', 'removedTypes.id')
            ->where(function ($query) use ($taskIds) {
                $query->whereIn('task_activity_logs.taskId', $taskIds)
                    ->orWhere('task_activity_logs.createdBy', $this->userId);
            })
            ->whereDate('task_activity_logs.created_at', $today)
            ->select([
                'task_activity_logs.*',
                'creator.name as createdBy',
                'creator.profile_picture as profile_picture',
                'added.name as addedUser',
                'removed.name as removedUser',
                'addedTypes.type as addedTaskTypes',
                'removedTypes.type as removedTaskTypes',
                \DB::raw('"activity" as type'),
            ])
            ->orderBy('task_activity_logs.created_at', 'DESC')
            ->get();
    }
}

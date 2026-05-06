<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\TaskCollaborator;
use App\Models\TaskComment;
use App\Models\TaskStatusLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FeedController extends Controller
{
    public function index()
    {
        return Inertia::render('Feeds/index');
    }

    public function show()
    {
        $userId = Auth::id();
        $filterTasks = Auth::user()->feed_scope == 0;

        $taskIds = $filterTasks
            ? TaskCollaborator::where('collaborator', $userId)
                ->orWhere('createdBy', $userId)
                ->distinct()
                ->pluck('taskId')
            : [];

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $daysAgo = $currentPage - 1;

        $startDate = ($currentPage == 1) ? now()->subDays(1)->startOfDay() : now()->subDays($daysAgo + 1)->startOfDay();

        $endDate = ($currentPage == 1) ? now()->endOfDay() : now()->subDays($daysAgo + 1)->endOfDay();

        $comments = TaskComment::when(!empty($taskIds), fn ($q) => $q->whereIn('taskId', $taskIds))
            ->join('tasks', 'task_comments.taskId', '=', 'tasks.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->selectRaw('
                task_comments.id, task_comments.taskId, task_comments.comments, task_comments.created_at,
                tasks.title AS taskTitle, projects.name AS projectName, "comment" AS type,
                (SELECT name FROM users WHERE users.id = task_comments.createdBy) AS CreatorName,
                (SELECT profile_picture FROM users WHERE users.id = task_comments.createdBy) AS profile_picture
            ')
            ->with([
                'getCommentImage:name,commentId',
                'replies' => fn ($q) => $q->with('getReplyImage', 'reply_creator')->orderBy('created_at', 'DESC'),
            ])
            ->where('reply_id', 0)
            ->whereBetween('task_comments.created_at', [$startDate, $endDate])
            ->orderBy('task_comments.id', 'DESC')
            ->get();

        $taskStatusLogs = TaskStatusLog::when(!empty($taskIds), fn ($q) => $q->whereIn('taskId', $taskIds))
            ->join('tasks', 'task_status_logs.taskId', '=', 'tasks.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->selectRaw('
                task_status_logs.*, tasks.title AS taskTitle, projects.name AS projectName, "status" AS type,
                (SELECT name FROM users WHERE task_status_logs.createdBy = users.id) AS createdByName,
                (SELECT profile_picture FROM users WHERE users.id = task_status_logs.createdBy) AS profile_picture
            ')
            ->whereBetween('task_status_logs.created_at', [$startDate, $endDate])
            ->orderBy('id', 'DESC')
            ->get();

        $feeds = Feed::with('task', 'project')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'desc')
            ->get();

        $combined = $comments->concat($taskStatusLogs)->concat($feeds);

        $combinedArray = $combined->sortByDesc('created_at')->values()->all();

        return response()->json($combinedArray);

    }
}

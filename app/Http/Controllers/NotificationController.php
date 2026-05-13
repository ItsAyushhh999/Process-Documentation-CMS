<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiJsonResponseTrait;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiJsonResponseTrait;

    public function create($data)
    {
        $notification = new Notification();
        $notification->task_id = data_get($data, 'taskId');
        $notification->notification = substr(strip_tags(data_get($data, 'notification')), 0, 200);
        $notification->created_by = $data['created_by'] ?? Auth::id();
        $notification->save();
        $notification->refresh();
        // broadcast to all collaborators and creator

        $taskId = data_get($data, 'taskId');

        $collaboratorIds = \App\Models\TaskCollaborator::where('taskId', $taskId)
                                            ->pluck('collaborator')
                                            ->toArray();

        $creatorId = \App\Models\Task::where('id', $taskId)
                            ->value('createdBy');

        $userIds = collect($collaboratorIds)
                    ->push($creatorId)
                    ->unique()
                    ->filter()
                    ->values();

        foreach($userIds as $userId)
        {
            broadcast(new \App\Events\NewNotificationEvent(
                [
                    'id' => $notification->id,
                    'notification' => $notification->notification,
                    'task_id' => $notification->task_id,
                    'created_by' => Auth::user()?->name,
                    'created_at' => $notification->created_at?->toDateTimeString() ?? now()->toDateTimeString(),
                    'task_name'    => Task::where('id', $notification->task_id)->value('title'), 
                    'is_read'      => false,
                ], $userId));
        }
    }

    public function getLatestNotifications(Request $request)
    {
       // dd('ok');
        $userId = Auth::id();
        $perPage = 10; // Number of notifications per page
        $page = (int) $request->input('page', 1);

        $notifications = Notification::selectRaw('notifications.*,
        (SELECT name FROM users WHERE users.id = notifications.created_by) AS created_by,
        (SELECT name FROM projects WHERE projects.id = tasks.project_id) AS task_name')
            ->where(function ($query) use ($userId) {
                // Filter notifications based on user's involvement: either as a collaborator or as the creator of the task
                $query->whereHas('collaborators', function ($query) use ($userId) {
                    $query->where('collaborator', $userId);
                })
                    ->orWhere('tasks.createdBy', $userId); // Or the user is the creator of the task
            })
            //->where('notifications.created_by', '!=', $userId) // Exclude notifications created by the authenticated user
            ->join('tasks', 'tasks.id', 'notifications.task_id')
            ->orderBy('id', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page);
        
            $data = collect($notifications->items())->map(function ($notif) use ($userId) {
                $notif->is_read = NotificationRead::where('notification_id', $notif->id)
                                        ->where('user_id', $userId)
                                        ->exists();
                return $notif;
            });

        return response()->json([
            'message' => 'Notifications fetched successfully.',
            'data'    => $data,
        ]);
    }

    // Mark a single notification as read
    public function markAsRead(Request $request, $id)
    {
        $userId = Auth::id();

        // Insert a read record for this user only — other users unaffected
        NotificationRead::firstOrCreate([
            'notification_id' => $id,
            'user_id' => $userId,
        ]);

        return response()->json([
            'message' => 'Notification marked as read.'
        ]);
    }

    // Mark all notifications as read for the authenticated user
    public function markAllAsRead(Request $request)
    {
        $userId = Auth::id();

        // Get all unread notification IDs for this user
        $unreadIds = Notification::whereHas('collaborators', fn ($q) => $q->where('collaborator', $userId))
                                ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $userId))
                                ->pluck('id');

        // Insert read records for this user only
        $records = $unreadIds->map(fn ($id) => [
            'notification_id' => $id,
            'user_id' => $userId,
            'read_at' => now(),
        ])->toArray();

        NotificationRead::insertOrIgnore($records);

        return response()->json(['message' => 'All marked as read.']);
    }

    // Count unread
    public function unreadCount(Request $request)
    {
        $userId = Auth::id();

        $count = Notification::where(function ($query) use ($userId) {
            $query->whereHas('collaborators', fn ($q) => $q->where('collaborator', $userId))
                    ->orwhereHas('task', fn ($q) => $q->where('createdBy', $userId));
        })
        ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $userId))
        ->count();

        return response()->json(['unread_count' => $count]);
    }
}

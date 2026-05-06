<?php

namespace App\View\Components;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class NotificationDrawer extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // Get the ID of the authenticated user
        $userId = Auth::id();

        // Fetch notifications along with the name of the creator and the name of the task associated with each notification
        $notifications = Notification::selectRaw('notifications.*,
            (SELECT name FROM users WHERE users.id = notifications.created_by) AS created_by,
            (SELECT name FROM projects WHERE projects.id = tasks.project_id) AS task_name
        ')
            ->where(function ($query) use ($userId) {
                // Filter notifications based on user's involvement: either as a collaborator or as the creator of the task
                $query->whereHas('collaborators', function ($query) use ($userId) {
                    $query->where('collaborator', $userId);
                })
                    ->orWhere('tasks.createdBy', $userId); // Or the user is the creator of the task
            })
            ->where('notifications.created_by', '!=', $userId) // Exclude notifications created by the authenticated user
            ->join('tasks', 'tasks.id', 'notifications.task_id')
            ->orderBy('id', 'DESC') // Order notifications by ID in descending order
            ->limit(10) // Limit the number of notifications to 10
            ->get();

        // Pass notifications data to the notification-drawer view for rendering
        return view('components.notification-drawer', compact('notifications'));
    }
}

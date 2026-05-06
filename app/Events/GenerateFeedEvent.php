<?php

namespace App\Events;

use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GenerateFeedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $feed;
    public ?string $taskId;
    public ?string $userScope;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($feed, ?string $taskId = null, ?string $userScope = null)
    {
        $this->feed = $feed;
        $this->taskId = $taskId;
        $this->userScope = $userScope;
    }

    public function broadcastOn()
    {
        $allUserIds = User::pluck('id')->toArray();

        $relatedUsersIds = isset($this->taskId)
            ? TaskCollaborator::join('tasks', 'task_collaborators.taskId', 'tasks.id')
                ->where('taskId', $this->taskId)
                ->pluck('task_collaborators.collaborator')
                ->merge(Task::where('id', $this->taskId)->pluck('createdBy'))
                ->unique()
                ->all()
            : $allUserIds;

        $channels = array_map(fn ($userId) => 'feed.' . $userId, $this->userScope == '0' ? $relatedUsersIds : $allUserIds);

        return $channels;
    }

    public function broadcastAs()
    {
        return 'my-event';
    }
}

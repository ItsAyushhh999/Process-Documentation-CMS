<?php

namespace App\Models;

use App\Models\NotificationRead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function collaborators()
    {
        return $this->hasMany(TaskCollaborator::class, 'taskId', 'task_id');
    }

    // used by markAllAsRead to scope to a user's notifications
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function reads()
    {
        return $this->hasMany(NotificationRead::class);
    }

    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}

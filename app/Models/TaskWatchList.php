<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TaskWatchList extends Model
{
    use HasFactory;
    protected $table = 'task_watchlists';
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'id', 'id');
    }

    public function scopeWatchList($query, $taskId, $userId)
    {
        return $query->where('task_id', $taskId)->where('user_id', $userId);
    }

    public function task(): HasOne
    {
        return $this->hasOne(Task::class, 'id', 'task_id')
            ->selectRaw('`id`, `title`, `priority`')
            ->addSelect([
                'project_name' => Project::select('name')->whereColumn('id', 'tasks.project_id')->take(1),
                'created_by'   => User::select('name')->whereColumn('id', 'tasks.createdBy')->take(1),
                'status'       => TaskStatus::select('name')->whereColumn('value', 'tasks.status')->take(1),
            ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\TaskObserver;

#[ObservedBy([TaskObserver::class])]
class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';

     protected $fillable = [
         'title',
         'description',
         'attachments',
         'project_id',
         'deadline',
         'priority',
         'status',
         'createdBy',
         'updatedBy'
     ];
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function attachment()
    {
        return $this->hasMany(Attachment::class);
    }

    public function collaborators()
    {
        return $this->hasMany(TaskCollaborator::class, 'taskId', 'id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'taskId', 'id');
    }

    public function taskType()
    {
        return $this->belongsToMany(TaskType::class, 'task_taskTypes_pivot', 'task_id', 'taskTypes_id')->withTimestamps();
    }

    public function assigned_by()
    {
        return $this->belongsTo(User::class, 'assignedBy', 'id');
    }

    public function completed_by()
    {
        return $this->belongsTo(User::class, 'completedBy', 'id');
    }

    public function taskStatusLog()
    {
        return $this->hasMany(TaskStatusLog::class, 'taskId', 'id');
    }

    /**
     * fetch collaborators email and slack username from users table.
     *
     * @return void
     */
    public function getTaskCollaboratorDetail()
    {
        return $this->hasManyThrough(User::class, TaskCollaborator::class, 'taskId', 'id', 'id', 'collaborator')->select('email', 'slack_username', 'flag', 'collaborator', 'name');
    }

    public function taskStatus()
    {
        return $this->hasOne(TaskStatus::class, 'value', 'status')->withDefault();
    }

    public function taskWatchList()
    {
        return $this->hasMany(TaskWatchList::class, 'task_id');
    }
}

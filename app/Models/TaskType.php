<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    use HasFactory;
    protected $table = 'taskTypes';

    protected $fillable = [
        'type',
        'created_by',
        'updated_by',
    ];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_taskTypes_pivot', 'taskTypes_id', 'task_id')->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCollaborator extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'collaborator', 'id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'taskId', 'id');
    }
}

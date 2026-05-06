<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatusLog extends Model
{
    protected $table = 'task_status_logs';

    use HasFactory;
    protected $guarded = [];
}

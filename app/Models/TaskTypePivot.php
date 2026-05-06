<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTypePivot extends Model
{
    use HasFactory;
    protected $table = 'task_taskTypes_pivot';

    protected $guarded = [];
}

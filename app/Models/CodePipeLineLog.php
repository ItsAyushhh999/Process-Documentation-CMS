<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodePipeLineLog extends Model
{
    use HasFactory;
    protected $table = 'code_pipeline_Logs';
    //    protected $fillable = [
    //        'created_by',
    //        'pull_request',
    //        'summary',
    //        'task_id',
    //        'project_name',
    //        'deploy',
    //    ];

    protected $guarded = [];

    public function task(): ?BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function updatedBy(): ?BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function project(): ?BelongsTo
    {
        return $this->belongsTo(project::class, 'project_id');
    }
}

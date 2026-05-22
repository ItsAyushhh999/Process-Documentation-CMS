<?php

namespace App\Models;

use App\Observers\TaskCommentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(TaskCommentObserver::class)]
class TaskComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getCommentImage()
    {
        return $this->hasMany(Attachment::class, 'commentId', 'id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'reply_id', 'id');
    }

    public function getReplyImage()
    {
        return $this->hasMany(Attachment::class, 'commentId', 'id');
    }

    public function reply_creator()
    {
        return $this->belongsTo(User::class, 'createdBy', 'id')->select('id', 'name', 'profile_picture');
    }

    public function checked_by()
    {
        return $this->belongsTo(User::class, 'checkedBy', 'id')->select('id', 'name');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'taskId', 'id');
    }
}

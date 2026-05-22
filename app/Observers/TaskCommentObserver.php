<?php

namespace App\Observers;

use App\Models\TaskComment;


class TaskCommentObserver
{
    /**
     * Handle the TaskComment "created" event.
     */
    public function created(TaskComment $taskComment): void
    {
       //
    }

    /**
     * Handle the TaskComment "updated" event.
     */
    public function updated(TaskComment $taskComment): void
    {
        //
    }

    /**
     * Handle the TaskComment "deleted" event.
     */
    public function deleted(TaskComment $taskComment): void
    {
        //
    }

    /**
     * Handle the TaskComment "restored" event.
     */
    public function restored(TaskComment $taskComment): void
    {
        //
    }

    /**
     * Handle the TaskComment "force deleted" event.
     */
    public function forceDeleted(TaskComment $taskComment): void
    {
        //
    }
}

<?php

namespace App\Models;

use App\Constants\FeedConstant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feed extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => strtoupper(FeedConstant::$FEED_STATUS[$value]),
        );
    }

    public function type(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => FeedConstant::$FEED_TYPES[$value],
        );
    }

    public function source(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => FeedConstant::$FEED_SOURCES[$value],
        );
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}

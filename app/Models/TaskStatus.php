<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'status',
        'createdBy',
        'updatedBy',
    ];

    public function created_by()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }
}

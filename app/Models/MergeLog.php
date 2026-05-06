<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MergeLog extends Model
{
    use HasFactory;
    protected $table = 'merges_logs';
    protected $guarded = [];
}

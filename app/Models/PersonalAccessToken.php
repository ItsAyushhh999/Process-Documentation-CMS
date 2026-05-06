<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    use HasFactory;

    protected $table = 'personal_access_tokens';
    protected $fillable = [
        'name', 'token', 'tokenable_type', 'tokenable_id', 'abilities', 'expires_at',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    public static array $defaultPermissions = [
        'DEV',
        'STAGING',
        'PRODUCTION',
    ];

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'permission_user');
    }
}

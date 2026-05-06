<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'updated_by',
        'phone',
        'secondary_phone',
        'profile_picture',
        'slack_username',
        'github_username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(self::class, 'updated_by');
    }

    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'permission_user');
    }

    public function userPermissions() : HasMany
    {
        return $this->hasMany(PermissionUser::class, 'user_id', 'id');
    }
}

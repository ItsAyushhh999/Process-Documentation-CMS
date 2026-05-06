<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;
    protected $table = 'projects';
    protected $hidden = ['pivot'];
    protected $fillable = [
        'name',
        'description',
        'url',
        'repository_name',
        'created_by',
        'updated_by',
        'sub_projects',
        'development_pipeline',
        'staging_pipeline',
        'production_Pipeline',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the subprojects of the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subprojects()
    {
        return $this->hasMany(self::class, 'sub_projects');
    }

    /**
     * Get the parent project of the subproject.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentProject()
    {
        return $this->belongsTo(self::class, 'sub_projects');
    }

    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'project_categories', 'project_id', 'category_id')
            // ->withPivot('position')
            ->orderByPivot('position', 'ASC')
            ->withTimestamps();
    }

    public function deploymentMappings()
    {
        return $this->hasMany(ProjectDeploymentMapping::class);

    }

    public function deploymentServer()
    {
        return $this->hasOne(ProjectDeploymentMapping::class, 'project_id', 'id');
    }
}

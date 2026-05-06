<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];
    protected $hidden = ['pivot'];

    protected $fillable = [
        'name',
        'project_id',
        'description',
        'updatedBy',
        'createdBy',
        'deleted_at',
        'position',
        'dir',
        'isPublished',
    ];

    // public function category(){
    //     return $this->hasMany(Category::class,'document_categories','project_id','category_id');
    // }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'document_categories')->withTimestamps();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeUpdater($query)
    {
        $query->selectSub(function ($sub) {
            $sub->select('name')->from('users')->whereColumn('users.id', 'documents.updatedBy');
        }, 'updatedBy');
    }
}

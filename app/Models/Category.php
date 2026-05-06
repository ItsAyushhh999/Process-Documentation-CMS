<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $hidden = ['pivot'];

    protected $fillable = [
        'name',
        'description',
        'logo',
        'created_by',
        'updated_by',
        'status',
    ];

    public function project()
    {
        return $this->belongsToMany(Project::class, 'project_categories')->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_categories')->withTimestamps();
    }
}

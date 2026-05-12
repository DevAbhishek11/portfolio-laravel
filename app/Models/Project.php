<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'short_description',
        'description',
        'thumbnail',
        'github_url',
        'live_url',
        'category',
        'status',
        'is_featured',
        'start_date',
        'end_date',
        'sort_order',
        'view_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'start_date'  => 'date',
        'end_date'    => 'date',
        'view_count'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function techStacks()
    {
        return $this->hasMany(ProjectTechStack::class);
    }

    public function pageViews()
    {
        return $this->morphMany(PageView::class, 'viewable');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}

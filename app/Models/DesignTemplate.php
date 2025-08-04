<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'template_files',
        'category',
        'tags',
        'created_by',
        'is_public'
    ];

    protected $casts = [
        'template_files' => 'array',
        'is_public' => 'boolean'
    ];

    /**
     * Get the designer who created this template
     */
    public function designer()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get tags as array
     */
    public function getTagsArrayAttribute()
    {
        return $this->tags ? explode(',', $this->tags) : [];
    }

    /**
     * Scope for public templates
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for templates by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeArticle extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'keywords', 'summary', 'content', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
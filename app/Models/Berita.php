<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'slug', 'title', 'image', 'date', 'location', 'author',
        'paragraphs', 'is_featured', 'views', 'likes',
    ];

    protected $casts = [
        'paragraphs'  => 'array',
        'is_featured' => 'boolean',
        'views'       => 'integer',
        'likes'       => 'integer',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

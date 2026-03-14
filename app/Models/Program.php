<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'slug', 'title', 'image', 'date', 'location', 'author',
        'status', 'month', 'year', 'intro', 'paragraphs',
        'has_report', 'report_file', 'views', 'likes',
    ];

    protected $casts = [
        'paragraphs'  => 'array',
        'has_report'  => 'boolean',
        'views'       => 'integer',
        'likes'       => 'integer',
    ];
}

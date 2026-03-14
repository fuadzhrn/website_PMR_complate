<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = ['title', 'path', 'year', 'month', 'uploaded_at'];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];
}

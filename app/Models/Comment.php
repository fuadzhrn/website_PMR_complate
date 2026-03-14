<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['berita_id', 'name', 'message'];

    public function berita()
    {
        return $this->belongsTo(Berita::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgMember extends Model
{
    protected $fillable = [
        'position_key', 'title', 'name', 'photo',
        'role_group', 'parent_key', 'sort_order', 'period',
        'angkatan', 'domisili',
    ];
}

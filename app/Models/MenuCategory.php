<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'image',
        'describtion'
    ];
}

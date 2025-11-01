<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'image',
        'description'
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'category_id');
    }
}

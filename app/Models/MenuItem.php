<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id',
        'item_name',
        'price',
        'tax_percentage',
        'photo'
    ];

    public function category(){
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

}

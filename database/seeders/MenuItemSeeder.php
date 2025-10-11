<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = MenuCategory::all();

        foreach($categories as $category){
            DB::table('menu_items')->insert([
                'category_id' => $category->id,
                'item_name' => 'HH' . $category->name,
                'price' => rand(1, 1000),
                'tax_percentage' => rand(1, 100),
                'photo' => 'images/MenuItem/' . $category->name . '_item' . 'jpg'
            ]);
        }
    }
}

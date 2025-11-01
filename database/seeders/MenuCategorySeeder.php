<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = ['Breakfast', 'Lunch', 'Dinner'];

        foreach($users as $user){
            DB::table('menu_categories')->insert([
                'user_id' => $user->id,
                'name' => $categories[$user->id - 1],
                'image' => 'storage/images/MenuCategory/' . $categories[$user->id - 1] . '.jpg',
                'description' => 'No describtion for now'
            ]);
        }
    }
}

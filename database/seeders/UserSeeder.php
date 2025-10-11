<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Abcde Fghij',
                'email' => 'abcde@gmail.com',
                'phone' => '0911111111',
                'business_name' => 'ABC Restaurant',
                'tin' => '1254896320',
                'password' => Hash::make('password1')
            ],
            [
                'name' => 'Klmno Pqrst',
                'email' => 'klmno@gmail.com',
                'phone' => '0922222222',
                'business_name' => 'KLM Cafe',
                'tin' => '1254896320',
                'password' => Hash::make('password2')
            ],
            [
                'name' => 'Ouvwx Yzzzz',
                'email' => 'ouvwx@gmail.com',
                'phone' => '0933333333',
                'business_name' => 'OUV Hotel',
                'tin' => '1254896320',
                'password' => Hash::make('password3')
            ]

        ]);
    }
}

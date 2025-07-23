<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Ali Seeker',
            'email' => 'seeker1@example.com',
            'password' => Hash::make('password'),
            'user_type_id' => 1, // seeker
            'city_id' => 1, // Karachi
        ]);

        User::create([
            'name' => 'Rehan Provider',
            'email' => 'provider1@example.com',
            'password' => Hash::make('password'),
            'user_type_id' => 2, // provider
            'city_id' => 2, // Lahore
        ]);
    }
}
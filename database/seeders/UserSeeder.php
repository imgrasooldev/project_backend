<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create specific users
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

        // Create 1000 fake users
        $faker = Faker::create();

        for ($i = 0; $i < 1000; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'), // default password for all
                'user_type_id' => rand(1, 2), // Randomly assign seeker (1) or provider (2)
                'city_id' => rand(1, 5), // Random city_id (adjust based on your cities table)
            ]);
        }
    }
}

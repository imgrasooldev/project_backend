<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        City::insert([
            ['name' => 'Karachi'],
            ['name' => 'Lahore'],
            ['name' => 'Islamabad'],
            ['name' => 'Faisalabad'],
            ['name' => 'Rawalpindi'],
        ]);
    }
}
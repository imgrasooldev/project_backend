<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Seeder;

class AreasTableSeeder extends Seeder
{
    public function run()
    {
        $areas = [
            ['name' => 'Gulshan-e-Maymar', 'city' => 'Karachi'],
            ['name' => 'Model Town', 'city' => 'Lahore'],
            ['name' => 'F-10', 'city' => 'Islamabad'],
            ['name' => 'Nazimabad', 'city' => 'Karachi'],
        ];

        foreach ($areas as $areaData) {
            $city = City::firstOrCreate(['name' => $areaData['city']]);
            Area::create([
                'name' => $areaData['name'],
                'city_id' => $city->id,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\ServiceProfile;
use App\Models\User;
use App\Models\Category;
use App\Models\Area;


class ServiceProfilesTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $categories = Category::all();
        $areas = Area::all();

        foreach ($users as $user) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                ServiceProfile::create([
                    'user_id' => $user->id,
                    'title' => $categories->random()->name,
                    'category_id' => $categories->random()->id,
                    'area_id' => $areas->random()->id,
                    'experience' => 'I have 3 years of experience.',
                    'available_days' => json_encode(['Mon', 'Wed', 'Fri']),
                    'available_time' => '10am - 5pm',
                ]);
            }
        }
    }
}

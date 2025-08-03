<?php

namespace Database\Seeders;

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
        $categories = Category::with('subcategories')->get();
        $areas = Area::all();

        $totalProfiles = 0;
        $maxProfiles = 1000;

        foreach ($users as $user) {
            $profilesForUser = rand(1, 3); // Each user can have 1 to 3 profiles.

            for ($i = 0; $i < $profilesForUser; $i++) {
                if ($totalProfiles >= $maxProfiles) {
                    return; // Stop after 1000 profiles.
                }

                $category = $categories->random();
                $subcategories = $category->subcategories;
                $subcategory = $subcategories->isNotEmpty() ? $subcategories->random() : null;

                ServiceProfile::create([
                    'user_id' => $user->id,
                    'title' => $this->generateTitle($category->name, $subcategory ? $subcategory->name : null),
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory ? $subcategory->id : null,
                    'area_id' => $areas->random()->id,
                    'experience' => 'I have ' . rand(1, 10) . ' years of experience.',
                    'available_days' => json_encode($this->randomDays()),
                    'available_time' => $this->randomTime(),
                ]);

                $totalProfiles++;
            }
        }
    }

    private function generateTitle($categoryName, $subcategoryName = null)
    {
        $verbs = ['I will', 'I can', 'Let me', 'I am here to', 'Your next'];
        $services = ['fix', 'install', 'repair', 'customize', 'setup', 'build', 'design', 'provide'];
        $adjectives = ['professional', 'expert', 'reliable', 'affordable', 'skilled', 'certified'];
        $endings = ['for you', 'in your area', 'with quality', 'at best rates', 'quickly', 'efficiently'];

        $role = strtolower($subcategoryName ?: $categoryName);

        $pattern = rand(1, 3);

        if ($pattern == 1) {
            return "{$verbs[array_rand($verbs)]} {$services[array_rand($services)]} your {$role}";
        } elseif ($pattern == 2) {
            return "{$verbs[array_rand($verbs)]} be your {$adjectives[array_rand($adjectives)]} {$role}";
        } else {
            return "{$verbs[array_rand($verbs)]} {$role} services {$endings[array_rand($endings)]}";
        }
    }

    private function randomDays()
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        shuffle($days);
        return array_slice($days, 0, rand(2, 5));
    }

    private function randomTime()
    {
        $startHours = rand(8, 12);
        $endHours = rand(4, 8) + 12; // Ensure end time is after start time

        return "{$startHours}am - {$endHours}pm";
    }
}

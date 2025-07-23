<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = ['Plumber', 'Electrician', 'Tutor', 'Mechanic', 'Driver', 'Cook', 'Cleaner'];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}

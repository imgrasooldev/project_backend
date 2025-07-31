<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Home Repair & Maintenance' => [
                'Electrician (Bijli ka kaam)',
                'Plumber (Pipes & Fitting)',
                'Carpenter (Furniture Banwana / Repair)',
                'Painter',
                'AC Technician',
                'Mason / Construction Worker',
                'Tiles / Marble Fitter',
                'Water Tank Cleaner',
                'CCTV Camera Installer',
            ],
            'Vehicle Services' => [
                'Car Mechanic',
                'Bike Mechanic',
                'Mobile Car Wash',
                'Car Electrician',
                'AC Gas Filling',
                'Battery Replacement',
                'Tyre Repair / Alignment',
                'Denter & Painter',
            ],
            'Education & Tuition' => [
                'Home Tutor (Grade 1–12)',
                'Quran Tutor (Online/Offline)',
                'Spoken English',
                'IELTS/TOEFL Instructor',
                'Computer Teacher',
                'Assignment/Thesis Helper',
                'Test Prep (CSS, MDCAT, ECAT)',
            ],
            'Health & Personal Care' => [
                'Home Nurse',
                'Physiotherapist',
                'Hijama / Cupping Specialist',
                'Female Beautician (Ghar aakar)',
                'Male Barber (Ghar aakar)',
                'Fitness Trainer',
                'Massage Therapist',
            ],
            'IT & Mobile Services' => [
                'Mobile Repairing',
                'Laptop/Desktop Repair',
                'Internet/WiFi Installation',
                'Freelance Web Developer',
                'Digital Marketing Expert',
                'Graphic Designer',
                'Data Recovery',
            ],
            'Events & Photography' => [
                'Photographer',
                'Videographer',
                'Event Decorator',
                'Makeup Artist',
                'Catering Service',
                'Stage / Lights Rental',
                'DJ / Sound Setup',
            ],
            'Domestic Help' => [
                'Cook (Male/Female)',
                'Maid (Part-time / Full-time)',
                'Babysitter',
                'Driver',
                'Guard / Watchman',
                'Ironing / Laundry',
                'Cleaning Service',
            ],
            'Office & Business Support' => [
                'Generator Technician',
                'Printer / Photocopier Repair',
                'Courier / Delivery Rider',
                'Office Cleaner',
                'Computer Operator',
                'Accountant',
                'Data Entry',
            ],
            'Tailoring & Fashion' => [
                'Ladies Tailor',
                'Men’s Tailor',
                'Embroidery Expert',
                'Alteration Services',
                'Boutique Designer',
                'Online Stitching Booking',
            ],
            'Others / Freelancers' => [
                'Translator',
                'Content Writer',
                'Voiceover Artist',
                'Social Media Manager',
                'YouTuber/Editor',
                'Video Animation / Editing',
                'Amazon/Online Business Expert',
            ],
        ];

        foreach ($data as $category => $subcategories) {
            $cat = Category::create(['name' => $category]);

            foreach ($subcategories as $sub) {
                Subcategory::create([
                    'name' => $sub,
                    'category_id' => $cat->id,
                ]);
            }
        }
    }
}

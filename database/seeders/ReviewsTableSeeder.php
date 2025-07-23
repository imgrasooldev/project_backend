<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\ServiceProfile;


class ReviewsTableSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $profiles = ServiceProfile::all();

        foreach ($profiles as $profile) {
            $seeker = $users->random();

            Review::create([
                'seeker_id' => $seeker->id,
                'provider_id' => $profile->user_id,
                'service_profile_id' => $profile->id,
                'rating' => rand(3, 5),
                'comment' => 'Good work and professional.',
            ]);
        }
    }
}

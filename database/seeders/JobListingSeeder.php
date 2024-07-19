<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\JobListing;

class JobListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            JobListing::create([
                'title' => $faker->jobTitle,
                'description' => $faker->paragraph,
                'company' => $faker->company,
                'skills' => $faker->words(3, true),
                'location' => $faker->country,
                'salary' => $faker->numberBetween(3000, 10000),
            ]);
        }
    }
}

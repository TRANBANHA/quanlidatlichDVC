<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BirthRegistrationSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('birth_registration')->insert([
                'user_id' => rand(1, 10), // Giả sử có 10 người dùng
                'full_name' => $faker->name,
                'birth_date' => $faker->date,
                'gender' => $faker->randomElement(['A', 'B', 'C']), // Giả sử A, B, C là các giới tính
                'ethnicity' => $faker->word,
                'nationality' => $faker->country,
                'birth_place' => $faker->city,
                'hometown' => $faker->city,
                'residence' => $faker->address,
                'father_name' => $faker->name,
                'father_cccd' => $faker->numerify('###########'),
                'mother_name' => $faker->name,
                'mother_cccd' => $faker->numerify('###########'),
                'relation' => $faker->word,
                'approval_status' => 1, // Hoặc 0 tùy ý
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
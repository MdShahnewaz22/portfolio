<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\SalaryAllowanceType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            MenuSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,

            CompanySeeder::class,

            AdminSeeder::class,
            Parsonal_InfoSeeder::class,
            SkillsSeeder::class,
            AboutSeeder::class,
            WorkExperienceSeeder::class,
            EducationSeeder::class,
            FeaturedProjectSeeder::class,
            BlogSeeder::class,

        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\WorkExperience;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            WorkExperience::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
            [
                'company_name'=>'RDTL',
                'year_to_year'=>'February, 2024 - Current',
                'designation'=>'Web Developer',
                'description'=>'Web developers use coding languages like HTML and CSS to design and create webpages, focusing on appearance and function.',
            ],
            [
                'company_name'=>'One Travel Planner Ltd',
                'year_to_year'=>'2023-2023',
                'designation'=>'UX Designer',
                'description'=>'Owing to advancements in product other designer technologies aute voluptate.',
            ]
        ];
    }
}

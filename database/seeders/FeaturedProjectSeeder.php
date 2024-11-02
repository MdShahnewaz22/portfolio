<?php

namespace Database\Seeders;

use App\Models\FeaturedProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeaturedProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            FeaturedProject::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
            [
                'project_name'=>'Hospital Management System',
                'live_link'=>'HTTPS://SMARTCARE.COM.BD/',
                'image'=>'featured_project/doctor 6.png',
            ],
            [
                'project_name'=>'ServiceShop',
                'live_link'=>'ServiceShop',
                'image'=>'featured_project/s1.jpg',
            ],
            [
                'project_name'=>'Case Management System',
                'live_link'=>'https://case-management.rdtl.xyz/',
                'image'=>'featured_project/case.png',
            ]
        ];
    }
}

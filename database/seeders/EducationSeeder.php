<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            Education::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
            [
                'institution_name' => 'Sotheast University',
                'year_to_year' => '2019-2023',
                'certificate_name' => 'BSc in Engineering',
                'group' => 'CSE',

            ],
            [
                'institution_name' => 'Adhyapak Abdul Mazid College',
                'year_to_year' => '2018-2019',
                'certificate_name' => 'Higher Secondary School Certificate(HSC)',
                'group' => 'Science',

            ],
            [
                'institution_name' => 'Darikandi Badda Asmatunnesa High school',
                'year_to_year' => '2016-2017',
                'certificate_name' => 'Secondary School Certificate (SSC)',
                 'group' => 'Science',

            ]
        ];
    }
}

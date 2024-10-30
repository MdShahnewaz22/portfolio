<?php

namespace Database\Seeders;

use App\Models\Skills;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            Skills::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
            [
                'title' =>'HTML',
                'percent' =>'90',
                'image' =>'skill/newaz.jpg',
                'file' =>'skill/cv.pdf',

            ],
            [
                'title' =>'CSS',
                'percent' =>'80',
                'image' =>'skill/newaz.jpg',
                'file' =>'skill/cv.pdf',

            ],
            [
                'title' =>'JS',
                'percent' =>'70',
                'image' =>'skill/newaz.jpg',
                'file' =>'skill/cv.pdf',

            ],
            [
                'title' =>'PHP',
                'percent' =>'90',
                'image' =>'skill/newaz.jpg',
                'file' =>'skill/cv.pdf',

            ],
            [
                'title' =>'Laravel',
                'percent' =>'70',
                'image' =>'skill/newaz.jpg',
                'file' =>'skill/cv.pdf',

            ],
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Parsonal_Info;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Parsonal_InfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            Parsonal_Info::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
            [
                'name' => 'Md.Shahnewaz',
                'designation' => 'Software Developer',
                'residence' => 'Bangladesh',
                'city' => 'Dhaka',
                'age' =>'24',
                'image' =>'parsonal_info/newaz.jpg'
            ]
        ];
    }
}

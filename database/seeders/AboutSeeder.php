<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            About::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
            [
                'phone'=>'01785915418',
                'gmail'=>'mdshahnewaz77@gmail.com',
                'github'=>'https://github.com/MdShahnewaz22',
                'skype'=>'shahnewaz@com',
                'language'=>'Bangla,English.',
                'years_experience'=>'1',
                'handled_project'=>'10',
                'open_source'=>'05',
                'awards'=>'1',
                'description'=>'Hi, my name is Md.Shahnewaz and I began using WordPress when first began. spent most of my waking hours for the last ten years designing, programming and operating WordPress sites go beyond with exclusive designer.',

            ]
        ];
    }
}

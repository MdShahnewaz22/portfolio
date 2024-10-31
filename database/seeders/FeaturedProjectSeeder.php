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
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            Blog::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
            [
                'image'=>'blog/blog1.png',
                'date'=>'1996-10-02',
                'title'=>'Elevate your mornings with perfectly brewed coffee ',
                'posted_by'=>'Adrinao Smith',
                'category'=>'Tips & Tricks, Design',
                'posted_on'=>'1973-11-21',
                'description'=>'Patent authorities globally are grappling with the challenge of redefining their approach to handling inventions generated not by human ingenuity but by AI. It has sparked considerable debate within the intellectual property community. ',

            ],
            [
                'image'=>'blog/blog4.png',
                'date'=>'1996-10-02',
                'title'=>'Mastering the clock: A guide to time management ',
                'posted_by'=>'Adrinao Smith',
                'category'=>'Tips & Tricks, Design',
                'posted_on'=>'1973-11-21',
                'description'=>'Patent authorities globally are grappling with the challenge of redefining their approach to handling inventions generated not by human ingenuity but by AI. It has sparked considerable debate within the intellectual property community. ',

            ],
            [
                'image'=>'blog/blog5.png',
                'date'=>'2001-11-22',
                'title'=>'Homeward bound: Crafting a productive home pffice',
                'posted_by'=>'Adrinao Smith',
                'category'=>'Tips & Tricks, Design',
                'posted_on'=>'1973-11-21',
                'description'=>'Patent authorities globally are grappling with the challenge of redefining their approach to handling inventions generated not by human ingenuity but by AI. It has sparked considerable debate within the intellectual property community. ',

            ]
        ];
    }
}

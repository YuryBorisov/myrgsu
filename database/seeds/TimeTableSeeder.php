<?php

use Illuminate\Database\Seeder;

class TimeTableSeeder extends Seeder
{

    private $times = [
        ['name' => '8:30-10:00'],
        ['name' => '10:10-11:40'],
        ['name' => '12:10-13:40'],
        ['name' => '13:50-15:20'],
        ['name' => '15:30-17:00'],
        ['name' => '17:10-18:40'],
        ['name' => '18:50-20:20'],
        ['name' => '20:30-22:00']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Time::truncate();
        \App\Models\Time::insert($this->times);
    }
}

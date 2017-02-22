<?php

use Illuminate\Database\Seeder;
use App\Models\Day;

class DayTableSeeder extends Seeder
{

    private $days = [
        ['name' => 'Понедельник'],
        ['name' => 'Вторник'],
        ['name' => 'Среда'],
        ['name' => 'Четверг'],
        ['name' => 'Пятница'],
        ['name' => 'Суббота'],
        ['name' => 'Воскресенье']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Day::truncate();
        Day::insert($this->days);
    }
}

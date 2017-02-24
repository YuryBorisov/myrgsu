<?php

use Illuminate\Database\Seeder;
use App\Models\Day;

class DayTableSeeder extends Seeder
{

    private $days = [
        ['id' => 1, 'name' => 'Понедельник'],
        ['id' => 2, 'name' => 'Вторник'],
        ['id' => 3, 'name' => 'Среда'],
        ['id' => 4, 'name' => 'Четверг'],
        ['id' => 5, 'name' => 'Пятница'],
        ['id' => 6, 'name' => 'Суббота'],
        ['id' => 0, 'name' => 'Воскресенье']
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

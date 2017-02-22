<?php

use Illuminate\Database\Seeder;

class WeekTableSeeder extends Seeder
{

    protected $weeks = [
        [
            'name' => 'Нечетная неделя',
            'active' => false
        ],
        [
            'name' => 'Четная неделя',
            'active' => true
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Week::truncate();
        \App\Models\Week::insert($this->weeks);
    }
}

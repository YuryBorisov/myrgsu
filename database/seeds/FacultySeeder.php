<?php

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{

    protected $faculties = [
        [
            'short_name' => 'ГФ',
            'full_name' => 'Гуманитарный факультет',
        ],
        [
            'short_name' => 'ФДО',
            'full_name' => 'Факультет довузовского образования',
        ],
        [
            'short_name' => 'ЛФ',
            'full_name' => 'Лингвистический факультет',
        ],
        [
            'short_name' => 'ФП',
            'full_name' => 'Факультет психологии',
        ],
        [
            'short_name' => 'ФСР',
            'full_name' => 'Факультет социальной работы',
        ],
        [
            'short_name' => 'ЭФ',
            'full_name' => 'Экономический факультет',
        ],
        [
            'short_name' => 'ФС',
            'full_name' => 'Факультет социологии',
        ],
        [
            'short_name' => 'ЮФ',
            'full_name' => 'Юридический факультет',
        ],
        [
            'short_name' => 'ФКМ',
            'full_name' => 'Факультет коммуникативного менеджмента',
        ],
        [
            'short_name' => 'ФУ',
            'full_name' => 'Факультет управления',
        ],
        [
            'short_name' => 'ФЗК',
            'full_name' => 'Факультет физической культуры',
        ],
        [
            'short_name' => 'ФИТ',
            'full_name' => 'Факультет информационных технологий',
        ],
        [
            'short_name' => 'ФЭиТБ',
            'full_name' => 'Факультет экологии и техносферной безопасности',
        ]
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Faculty::truncate();
        Faculty::insert($this->faculties);
    }
}

<?php

use Illuminate\Database\Seeder;
use \App\Models\Location;

class LocationSeeder extends Seeder
{

    private $locations = [
        ['name' => 'Общежитие №1'],
        ['name' => 'Общежитие №2'],
        ['name' => 'Общежитие №3'],
        ['name' => 'Общежитие №4'],
        ['name' => 'Общежитие №5'],
        ['name' => 'Москва'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Location::truncate();
        Location::insert($this->locations);
    }
}

<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(DayTableSeeder::class);
        $this->call(TimeTableSeeder::class);
        $this->call(WeekTableSeeder::class);
        $this->call(FacultySeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(LocationSeeder::class);
    }
}

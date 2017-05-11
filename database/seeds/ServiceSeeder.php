<?php

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{

    private $services = [
        [
            'name' => 'vk'
        ],
        [
            'name' => 'telegram'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::truncate();
        Service::insert($this->services);
    }
}

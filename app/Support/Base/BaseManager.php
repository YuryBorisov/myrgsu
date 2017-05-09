<?php

namespace App\Support\Base;

abstract class BaseManager
{

    protected $data;

    protected $user;

    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    public abstract function run();

}
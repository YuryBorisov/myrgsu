<?php

namespace App\Support\Base;

abstract class BaseCommand
{

    protected $commands = [];

    protected $user;

    protected $data;

    protected $message;

    protected $text;

    public function __construct($user, $message, $data = [])
    {
        $this->user = $user;
        $this->message = $message;
        $this->data = $data;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public abstract function view($title = true);

}
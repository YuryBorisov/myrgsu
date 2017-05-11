<?php

namespace App\Repositories;

use \Illuminate\Support\Facades\Cache;

abstract class BaseRepository
{

    private static $instances = [];

    /**
     * Model
     *
     * @var string
     */
    protected $model;

    /**
     * Cache name for tags
     *
     * @var string
     */
    protected $cacheName = 'base';

    /**
     * Method query by id
     *
     * @var string
     */
    protected $getMethodById = 'find';

    /**
     * Method query by name
     *
     * @var string
     */
    protected $getMethodName = 'whereName';

    /**
     * Key
     *
     * @var string
     */
    protected $addId = 'id';

    protected $addName = 'name';

    protected $isAddName = true;

    protected function getCachedById()
    {
        return Cache::tags([$this->cacheName, 'id']);
    }

    protected function getCachedByName()
    {
        return Cache::tags([$this->cacheName, 'name']);
    }

    protected function isById($id, $serviceId = 0)
    {
        return $this->getCachedById()->has($id);
    }

    protected function isByName($name)
    {
        return $this->getCachedByName()->has($name);
    }

    protected function addById($id, $data)
    {
        $this->getCachedById()->forever($id, $data);
    }

    protected function addByName($name, $data)
    {
        $this->getCachedByName()->forever($name, $data);
    }

    protected function add($id, $name, $data)
    {
        $this->addById($id, $data);
        $this->addByName($name, $data);
    }

    public function get($id, $serviceId = 0)
    {
        return $this->getById($id, $serviceId);
    }

    public function clear($id, $name = null)
    {
        $this->getCachedById()->forget($id);
        if($this->isAddName)
        {
            $this->getCachedByName()->forget($name);
        }
    }

    public function getById($id, $serviceId = 0)
    {
        if(!$this->isById($id))
        {
            if($r = call_user_func([$this->model, $this->getMethodById], $id))
            {
                $r = $r->toArray();
                $this->addById($id, $r);
                if($this->isAddName)
                {
                    $this->add($id, $r[$this->addName], $r);
                }
                return $r;
            }
            return false;
        }
        return $this->getCachedById()->get($id);
    }

    public function getByName($name)
    {
        if(!$this->isByName($name))
        {
            if($r = call_user_func([$this->model, $this->getMethodName], $name)->first())
            {
                $r = $r->toArray();
                $this->add($this->addId, $name, $r);
                return $r;
            }
            return false;
        }
        return $this->getCachedByName()->get($name);
    }

    public final static function instance()
    {
        if(!array_key_exists(static::class, self::$instances))
        {
            self::$instances[static::class] = new static;
        }
        return self::$instances[static::class];
    }

    public final static function  getInstanceRepository($class)
    {

    }

}
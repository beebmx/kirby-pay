<?php

namespace Beebmx\KirbyPay\Concerns;

use Beebmx\KirbyPay\Drivers\Factory;
use Beebmx\KirbyPay\Resource;
use Illuminate\Support\Traits\ForwardsCalls;

trait ManagesResources
{
    use ForwardsCalls;

    protected $driver;

    protected $resource;

    public function __construct()
    {
        $this->driver = (new Factory)->find();

        $this->boot();
    }

    public function newResource()
    {
        return new Resource(static::$type, static::$path);
    }

    public function boot()
    {
    }

    protected static function getDriver()
    {
        return (new Factory)->find();
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->newResource(), $method, $parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}

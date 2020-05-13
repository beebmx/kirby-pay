<?php

namespace Beebmx\KirbyPay\Drivers;

use Illuminate\Support\Str;

class Factory
{
    public function find()
    {
        return static::getDriver();
    }

    protected function getDriver()
    {
        $class = $this->getClass();

        if (!class_exists($class)) {
            throw new DriverException('The driver requested does not exists');
        }

        return new $class;
    }

    protected function getClass()
    {
        return 'Beebmx\\KirbyPay\\Drivers\\' . Str::studly($this->getService() . 'Driver');
    }

    protected function getService()
    {
        return option('beebmx.kirby-pay.service', 'sandbox');
    }
}

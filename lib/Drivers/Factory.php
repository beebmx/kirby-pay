<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Exception\DriverException;
use Illuminate\Support\Str;

class Factory
{
    /**
     * Find the current service driver
     *
     * @return Driver
     * @throws DriverException
     */
    public function find()
    {
        return static::getDriver();
    }

    /**
     * Get an instance of the current service driver
     *
     * @return Driver
     * @throws DriverException
     */
    protected function getDriver()
    {
        $class = $this->getClass();

        if (!class_exists($class)) {
            throw new DriverException('The driver requested does not exists');
        }

        return new $class;
    }

    /**
     * Get the current driver class for the current service
     *
     * @return string
     */
    protected function getClass()
    {
        return 'Beebmx\\KirbyPay\\Drivers\\' . Str::studly($this->getService() . 'Driver');
    }

    /**
     * Get the current service
     *
     * @return string
     */
    protected function getService()
    {
        return option('beebmx.kirby-pay.service', 'sandbox');
    }
}

<?php

namespace Beebmx\KirbyPay\Drivers;

use Exception;
use ReflectionClass;

class Factory
{
    public function find()
    {
        return static::getDriver();
    }

    protected function getDriver()
    {
        $class = $this->getClass();

        if ($this->isInstantiable($class) && class_exists($class)) {
            return new $class;
        }

        throw new Exception('The driver requested does not exists');
    }

    protected function isInstantiable(string $service)
    {
        return $this->getCurrentClass()->isInstantiable();
    }

    protected function getCurrentNamespace()
    {
        return $this->getCurrentClass()->getNamespaceName();
    }

    protected function getCurrentClass()
    {
        try {
            return new ReflectionClass($this);
        } catch (Exception $e) {
            return null;
        }
    }

    protected function getClass()
    {
        return $this->getCurrentNamespace() . '\\' . ucfirst($this->getService() . 'Driver');
    }

    protected function getService()
    {
        return option('beebmx.kirby-pay.service', 'sandbox');
    }
}

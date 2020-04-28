<?php

namespace Beebmx\KirbyPay\Concerns;

use Illuminate\Support\Collection;

trait ManagesRoutes
{
    public static function getBaseApiPath()
    {
        return option('beebmx.kirby-pay.api_path', 'beebmx/kirby-pay/');
    }

    public function getMethodByName(string $key)
    {
        foreach ($this->all() as $index => $route) {
            if ($route['name'] === $key) {
                return $this->all()[$index];
            }
        }
        return false;
    }

    public function getRoutePathByName(string $key)
    {
        return $this->getRouteOptionByMethod($key, 'pattern');
    }

    public function getRouteMethodByName(string $key)
    {
        return $this->getRouteOptionByMethod($key, 'method');
    }

    protected function getRouteOptionByMethod(string $key, string $option = '')
    {
        return strtolower($this->getMethodByName($key)[$option]);
    }
}

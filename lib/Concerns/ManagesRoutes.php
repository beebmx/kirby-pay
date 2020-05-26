<?php

namespace Beebmx\KirbyPay\Concerns;

trait ManagesRoutes
{
    public static function getBaseApiPath()
    {
        return option('beebmx.kirby-pay.api_path', '/beebmx/kirby-pay/');
    }

    public static function getMethodByName(string $key)
    {
        foreach (static::all() as $index => $route) {
            if ($route['name'] === $key) {
                return static::all()[$index];
            }
        }
        return false;
    }

    public static function getRoutePathByName(string $key)
    {
        return static::getRouteOptionByMethod($key, 'pattern');
    }

    public static function getRouteMethodByName(string $key)
    {
        return static::getRouteOptionByMethod($key, 'method');
    }

    protected static function getRouteOptionByMethod(string $key, string $option = '')
    {
        return strtolower(static::getMethodByName($key)[$option]);
    }
}

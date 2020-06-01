<?php

namespace Beebmx\KirbyPay\Concerns;

trait ManagesRoutes
{
    /**
     * Get base path for api
     *
     * @return string
     */
    public static function getBaseApiPath()
    {
        return option('beebmx.kirby-pay.api_path', '/beebmx/kirby-pay/');
    }

    /**
     * Get method by given name key
     *
     * @param string $key
     * @return string|bool
     */
    public static function getMethodByName(string $key)
    {
        foreach (static::all() as $index => $route) {
            if ($route['name'] === $key) {
                return static::all()[$index];
            }
        }
        return false;
    }

    /**
     * Get route path by name key
     *
     * @param string $key
     * @return string
     */
    public static function getRoutePathByName(string $key)
    {
        return static::getRouteOptionByMethod($key, 'pattern');
    }

    /**
     * Get route method by name key
     *
     * @param string $key
     * @return string
     */
    public static function getRouteMethodByName(string $key)
    {
        return static::getRouteOptionByMethod($key, 'method');
    }

    /**
     * Get route option by method
     *
     * @param string $key
     * @param string $option
     * @return string
     */
    protected static function getRouteOptionByMethod(string $key, string $option = '')
    {
        return strtolower(static::getMethodByName($key)[$option]);
    }
}

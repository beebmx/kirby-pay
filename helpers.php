<?php

use Beebmx\KirbyPay\Routes\Routes;

if (!function_exists('pay')) {
    function pay($key, $default = null)
    {
        return option("beebmx.kirby-pay.{$key}", $default);
    }
}

if (!function_exists('kpStyle')) {
    function kpStyle($key, $default = null)
    {
        return option("beebmx.kirby-pay.styles", $default)[$key] ?? $default;
    }
}

if (!function_exists('kpStripe')) {
    function kpStripe()
    {
        return [
            'base' => array_merge(
                kpStyle('stripe-input-default'),
                kpStyle('stripe-input'),
            ),
            'invalid' => array_merge(
                kpStyle('stripe-invalid-default'),
                kpStyle('stripe-invalid'),
            ),
        ];
    }
}

if (!function_exists('kpT')) {
    function kpT($key, $default = null)
    {
        return t("beebmx.kirby-pay.".$key, $default);
    }
}

if (!function_exists('payroute')) {
    function kpUrl($key)
    {
        return (new Routes)->getRoutePathByName($key);
    }
}

if (!function_exists('payroute')) {
    function kpMethod($key)
    {
        return (new Routes)->getRouteMethodByName($key);
    }
}

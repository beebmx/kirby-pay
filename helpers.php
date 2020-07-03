<?php

use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Routes\Routes;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Illuminate\Support\Collection;

if (!function_exists('pay')) {
    function pay($key, $default = null)
    {
        return option("beebmx.kirby-pay.{$key}", $default);
    }
}

if (!function_exists('kpStyle')) {
    function kpStyle($key, $default = null)
    {
        return option('beebmx.kirby-pay.styles', $default)[$key] ?? $default;
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
        return t('beebmx.kirby-pay.' . $key, $default);
    }
}

if (!function_exists('kpUrl')) {
    function kpUrl($key)
    {
        return Routes::getRoutePathByName($key);
    }
}

if (!function_exists('kpMethod')) {
    function kpMethod($key)
    {
        return Routes::getRouteMethodByName($key);
    }
}

if (!function_exists('kpPaymentMethods')) {
    function kpPaymentMethods()
    {
        $methods = Payment::getPaymentMethods();

        return (new Collection(pay('payment_types')))
            ->filter(function ($method) use ($methods) {
                return (in_array($method, $methods));
            })->values()->toArray();
    }
}

if (!function_exists('kpInDevelopment')) {
    function kpInDevelopment()
    {
        return in_array(pay('env', 'production'), ['test', 'local']);
    }
}

if (!function_exists('kpGetFirstPaymentMethod')) {
    function kpGetFirstPaymentMethod()
    {
        return kpPaymentMethods()[0] ?? 'card';
    }
}

if (!function_exists('kpHasShipping')) {
    function kpHasShipping(): bool
    {
        return pay('shipping', false);
    }
}

if (!function_exists('kpParseMoney')) {
    function kpParseMoney($value) {
        return Money::of(
            $value,
            strtoupper(pay('currency', 'usd')),
            new CustomContext(pay('money_precision', 2))
        )->formatTo(
            pay('locale', 'en_US')
        );
    }
}

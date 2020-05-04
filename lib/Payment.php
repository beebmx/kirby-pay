<?php

namespace Beebmx\KirbyPay;

use Illuminate\Support\Collection;

class Payment extends Model
{
    protected static $path = 'payment';

    protected static $type = '.json';

    public static function order(Collection $customer, Collection $items, string $token = null, string $type = 'card', Collection $shipping = null)
    {
        $customer = Customer::firstOrCreate($customer, $token, $type);

        return static::write(
            static::getDriver()->createOrder(new Collection($customer), $items, $token, $type, $shipping)
        );
    }

    public static function charge(Collection $customer, Collection $items, string $token = null, string $type = 'card', Collection $shipping = null)
    {
        return static::write(
            static::getDriver()->createCharge(new Collection($customer), $items, $token, $type, $shipping)
        );
    }

    public static function serviceUrl()
    {
        return static::getDriver()->getUrls()['payments'];
    }

    public static function getPaymentMethods()
    {
        return static::getDriver()->getPaymentMethods();
    }

    public static function parseAmount($amount)
    {
        return static::getDriver()->parsePrice($amount);
    }
}

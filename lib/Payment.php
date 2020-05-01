<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Concerns\ManagesResources;
use Beebmx\KirbyPay\Contracts\Resourceable;
use Illuminate\Support\Collection;

class Payment implements Resourceable
{
    use ManagesResources;

    protected static $path = 'payment';

    protected static $type = '.json';

    public static function order(Collection $customer, Collection $items, string $token, string $type = 'card', Collection $shipping = null)
    {
        $customer = Customer::firstOrCreate($customer, $token, $type);

        return static::write(
            static::getDriver()->createOrder(new Collection($customer), $items, $token, $type, $shipping)
        );
    }

    public static function charge(Collection $customer, Collection $items, string $token, string $type = 'card', Collection $shipping = null)
    {
        return static::write(
            static::getDriver()->createCharge(new Collection($customer), $items, $token, $type, $shipping)
        );
    }

    public static function serviceUrl()
    {
        return static::getDriver()->getUrls()['payments'];
    }
}

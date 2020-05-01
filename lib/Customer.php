<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Concerns\ManagesResources;
use Beebmx\KirbyPay\Contracts\Resourceable;
use Illuminate\Support\Collection;

class Customer implements Resourceable
{
    use ManagesResources;

    protected static $path = 'customer';

    protected static $type = '.json';

    public static function create(Collection $customer, $token, $method = null)
    {
        return static::write(
            static::getDriver()->createCustomer($customer, $token, $method)
        );
    }

    public static function firstOrCreate(Collection $promise, $token, $method = null)
    {
        if ($found = static::email($promise['email'])) {
            return $found;
        }

        return static::create($promise, $token, $method);
    }

    public static function email(string $email)
    {
        return static::search($email, 'email')->first();
    }

    public static function serviceUrl()
    {
        return static::getDriver()->getUrls()['customers'];
    }
}

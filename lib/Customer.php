<?php

namespace Beebmx\KirbyPay;

use Illuminate\Support\Collection;

class Customer extends Model
{
    protected static $path = 'customer';

    protected static $type = '.json';

    public static function create(Collection $customer, $token, $method = null)
    {
        return static::write(
            static::driver()->createCustomer($customer, $token, $method)
        );
    }

    public static function firstOrCreate(Collection $promise, $token, $method = null)
    {
        if ($found = static::email($promise['email'])) {
            return $found->toArray();
        }

        return static::create($promise, $token, $method)->toArray();
    }

    public static function email(string $email)
    {
        return static::search($email, 'email')->first();
    }

    public static function serviceUrl()
    {
        return static::driver()->getUrls()['customers'];
    }
}

<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Elements\Buyer;

class Customer extends Model
{
    protected static $path = 'customer';

    public static function create(Buyer $customer, $token, $method = null)
    {
        return static::write(
            static::driver()->createCustomer($customer, $token, $method)->toArray()
        );
    }

    public static function firstOrCreate(Buyer $promise, $token, $method = null)
    {
        if ($found = static::email($promise->email)) {
            return $found;
        }

        return static::create($promise, $token, $method);
    }

    public static function email(string $email)
    {
        return static::search($email, 'email')->first();
    }

    public static function serviceUrl(): string
    {
        return static::driver()->getUrls()['customers'];
    }
}

<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Concerns\ManagesResources;
use Beebmx\KirbyPay\Contracts\Resource;
use Illuminate\Support\Collection;

class Customer implements Resource
{
    use ManagesResources;

    protected static $path = 'customer';

    protected static $type = '.json';

    public function create(Collection $customer, $token, $method = null)
    {
        return $this->write(
            $this->getDriver()->createCustomer($customer, $token, $method)
        );
    }

    public static function firstOrCreate(Collection $promise, $token, $method = null)
    {
        $customer = (new self);

        if ($found = $customer->email($promise['email'])) {
            return $found;
        }

        return $customer->create($promise, $token, $method);
    }

    public function email(string $email)
    {
        $customer = $this->search($email, 'email')->collection()->first();
        return $customer
            ? $customer->toArray()
            : false;
    }
}

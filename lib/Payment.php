<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Concerns\ManagesResources;
use Beebmx\KirbyPay\Contracts\Resource;
use Illuminate\Support\Collection;

class Payment implements Resource
{
    use ManagesResources;

    protected static $path = 'payment';

    protected static $type = '.json';

    public function order(Collection $customer, Collection $items, string $token, string $type = 'card', Collection $shipping = null)
    {
        $customer = Customer::firstOrCreate($customer, $token, $type);

        return $this->write(
            $this->getDriver()->createOrder(new Collection($customer), $items, $token, $type, $shipping)
        );
    }

    public function charge(Collection $customer, Collection $items, string $token, string $type = 'card', Collection $shipping = null)
    {
        return $this->write(
            $this->getDriver()->createCharge(new Collection($customer), $items, $token, $type, $shipping)
        );
    }
}

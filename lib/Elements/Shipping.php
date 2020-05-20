<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;

class Shipping implements Elementable
{
    public $address;

    public $state;

    public $city;

    public $postal_code;

    public $country;

    public function __construct(string $address, string $postal_code = null, string $city = null, string $state = null, string $country = null)
    {
        $this->address = $address;
        $this->city = $city;
        $this->country = $country;
        $this->state = $state;
        $this->postal_code = $postal_code;
    }

    public function toArray(): array
    {
        return [
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
        ];
    }
}

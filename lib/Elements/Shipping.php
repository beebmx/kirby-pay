<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;

class Shipping implements Elementable
{
    /**
     * Shipping address
     *
     * @var string
     */
    public $address;

    /**
     * Shipping state
     *
     * @var string|null
     */
    public $state;

    /**
     * Shipping city
     *
     * @var string|null
     */
    public $city;

    /**
     * Shipping postal code
     *
     * @var string|null
     */
    public $postal_code;

    /**
     * Shipping country
     *
     * @var string|null
     */
    public $country;

    /**
     * Create an instance of Shipping
     *
     * @param string $address
     * @param string|null $postal_code
     * @param string|null $city
     * @param string|null $state
     * @param string|null $country
     */
    public function __construct(string $address, string $postal_code = null, string $city = null, string $state = null, string $country = null)
    {
        $this->address = $address;
        $this->city = $city;
        $this->country = $country;
        $this->state = $state;
        $this->postal_code = $postal_code;
    }

    /**
     * Get the attributes of Shipping as array
     *
     * @return array
     */
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

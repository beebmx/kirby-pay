<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;

class Customer implements Elementable
{
    /**
     * Customer ID
     *
     * @var string|int
     */
    public $id;

    /**
     * Customer email
     *
     * @var string
     */
    public $email;

    /**
     * Customer information
     *
     * @var Buyer
     */
    public $buyer;

    /**
     * Customer payment source
     *
     * @var Source
     */
    public $source;

    /**
     * Create an instance of Customer element
     *
     * @param $id
     * @param $email
     * @param Buyer $buyer
     * @param Source $source
     */
    public function __construct($id, $email, Buyer $buyer, Source $source)
    {
        $this->id = $id;
        $this->email = $email;
        $this->buyer = $buyer;
        $this->source = $source;
    }

    /**
     * Get the attributes of Customer as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'customer' => $this->buyer->toArray(),
            'source' => $this->source->toArray(),
        ];
    }
}

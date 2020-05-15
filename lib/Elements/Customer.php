<?php

namespace Beebmx\KirbyPay\Elements;

class Customer
{
    public $id;

    public $email;

    public $buyer;

    public $source;

    public function __construct($id, $email, Buyer $buyer, Source $source)
    {
        $this->id = $id;
        $this->email = $email;
        $this->buyer = $buyer;
        $this->source = $source;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'customer' => $this->buyer->toArray(),
            'source' => $this->source->toArray(),
        ];
    }
}

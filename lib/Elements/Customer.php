<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;

class Customer implements Elementable
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

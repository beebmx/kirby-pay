<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;

class Buyer implements Elementable
{
    public $name;

    public $email;

    public $phone;

    public $id;

    public $customer_id;

    public function __construct(string $name, string $email, string $phone = null, $id = null, $customer_id = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->id = $id;
        $this->customer_id = $customer_id ?? $id;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'id' => $this->id,
            'customer_id' => $this->customer_id,
        ];
    }
}

<?php

namespace Beebmx\KirbyPay\Elements;

class Buyer
{
    public $name;

    public $email;

    public $phone;

    public function __construct(string $name, string $email, string $phone = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}

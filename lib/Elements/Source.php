<?php

namespace Beebmx\KirbyPay\Elements;

class Source
{
    public $id;

    public $name;

    public $last4;

    public $type;

    public $brand;

    public function __construct($id, string $name, $last4, $type = null, $brand = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->last4 = $last4;
        $this->type = $type;
        $this->brand = $brand;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last4' => $this->last4,
            'type' => $this->type,
            'brand' => $this->brand,
        ];
    }
}

<?php

namespace Beebmx\KirbyPay\Elements;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Charge
{
    public $id;

    public $currency;

    public $amount;

    public $status;

    public $customer;

    public $items;

    public $shipping;

    public $extra;

    public function __construct($id, string $status, Buyer $customer, Items $items, Shipping $shipping = null, array $extra = null)
    {
        $this->id = $id;
        $this->status = $status;
        $this->customer = $customer;
        $this->items = $items;
        $this->shipping = $shipping;
        $this->extra = $extra;

        $this->currency = Str::upper(pay('currency', 'usd'));
        $this->amount = $items->amount();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'type' => 'charge',
            'customer' => $this->customer->toArray(),
            'items' => $this->items->toArray(),
            'shipping' => $this->shipping ? $this->shipping->toArray() : [],
            'extra' => $this->extra ? $this->extra : [],
        ];
    }
}

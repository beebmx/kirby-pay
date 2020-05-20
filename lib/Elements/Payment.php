<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;
use Illuminate\Support\Str;

abstract class Payment implements Elementable
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

    public function toArray(): array
    {
        return [];
    }
}

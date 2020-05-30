<?php

namespace Beebmx\KirbyPay\Elements;

class Order extends Payment
{
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'type' => 'order',
            'email' => $this->customer->email,
            'customer' => $this->customer->toArray(),
            'items' => $this->items->toArray(),
            'shipping' => $this->shipping ? $this->shipping->toArray() : [],
            'extra' => $this->extra ? $this->extra : [],
        ];
    }
}

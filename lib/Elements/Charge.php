<?php

namespace Beebmx\KirbyPay\Elements;

class Charge extends Payment
{
    /**
     * Get the attributes of Charge as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'currency' => $this->currency,
            'amount' => $this->amount,
            'type' => 'charge',
            'email' => $this->customer->email,
            'customer' => $this->customer->toArray(),
            'items' => $this->items->toArray(),
            'shipping' => $this->shipping ? $this->shipping->toArray() : [],
            'extra' => $this->extra ? $this->extra : [],
        ];
    }
}

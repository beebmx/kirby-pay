<?php

namespace Beebmx\KirbyPay\Elements;

use Illuminate\Support\Collection;

class Items
{
    public $items;

    public function __construct()
    {
        $this->items = new Collection;
    }

    public function put(Item $item)
    {
        $this->items->push($item);
    }

    public function count()
    {
        return $this->items->count();
    }

    public function amount()
    {
        return $this->items->sum(function($item) {
            return $item->amount * $item->quantity;
        });
    }

    public function totalQuantity()
    {
        return $this->items->sum(function($item) {
            return $item->quantity;
        });
    }

    public function all()
    {
        return $this->items;
    }

    public function toArray()
    {
        return $this->items->map(function($item) {
            return $item->toArray();
        })->toArray();
    }
}

<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;
use Beebmx\KirbyPay\Exception\ItemException;
use Illuminate\Support\Collection;

class Items implements Elementable
{
    /**
     * Collection of Item
     *
     * @var Collection
     */
    public $items;

    /**
     * Create an instance of Items
     *
     * @param array $items
     * @throws ItemException
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            if (!$item instanceof Item) {
                throw new ItemException('Must provide an instance of Item Element');
            }
        }

        $this->items = new Collection($items);
    }

    /**
     * Push an Item into items collection
     *
     * @param Item $item
     */
    public function put(Item $item)
    {
        $this->items->push($item);
    }

    /**
     * Count the number ot item
     *
     * @return int
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Get the sum of Item amount
     *
     * @return float
     */
    public function amount(): float
    {
        return $this->items->sum(function ($item) {
            return $item->amount * $item->quantity;
        });
    }

    /**
     * Get the sum of Item quantity
     *
     * @return int
     */
    public function totalQuantity(): int
    {
        return $this->items->sum(function ($item) {
            return $item->quantity;
        });
    }

    /**
     * Get collection of all Item
     *
     * @return Collection
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Get the attributes of Items as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items->map(function ($item) {
            return $item->toArray();
        })->toArray();
    }
}

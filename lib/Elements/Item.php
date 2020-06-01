<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;

class Item implements Elementable
{
    /**
     * Item name
     *
     * @var string
     */
    public $name;

    /**
     * Item amount
     *
     * @var float
     */
    public $amount;

    /**
     * Item quantity
     *
     * @var int
     */
    public $quantity;

    /**
     * Item ID when exists
     *
     * @var string|null
     */
    public $id;

    /**
     * Create an instance of Item
     *
     * @param string $name
     * @param float $amount
     * @param int $quantity
     * @param string|null $id
     */
    public function __construct(string $name, float $amount, int $quantity, $id = null)
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->quantity = $quantity;
        $this->id = $id;
    }

    /**
     * Get the attributes of an Item as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'quantity' => $this->quantity,
        ];
    }
}

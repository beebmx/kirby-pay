<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;

class Source implements Elementable
{
    /**
     * Source ID
     *
     * @var string|int
     */
    public $id;

    /**
     * Source buyer name
     *
     * @var string
     */
    public $name;

    /**
     * Source last 4 digist of card
     *
     * @var string
     */
    public $last4;

    /**
     * Source type
     *
     * @var string|null
     */
    public $type;

    /**
     * Source brand of card
     *
     * @var string|null
     */
    public $brand;

    /**
     * Create an instance of Source
     *
     * @param $id
     * @param string $name
     * @param $last4
     * @param null $type
     * @param null $brand
     */
    public function __construct($id, string $name, $last4, $type = null, $brand = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->last4 = $last4;
        $this->type = $type;
        $this->brand = $brand;
    }

    /**
     * Get the attributes of Source as array
     *
     * @return array
     */
    public function toArray(): array
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

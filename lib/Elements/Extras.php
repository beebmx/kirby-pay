<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;
use Beebmx\KirbyPay\Exception\ExtraException;
use Illuminate\Support\Collection;

class Extras implements Elementable
{
    /**
     * Collection of Extras
     *
     * @var Collection
     */
    public $extras;

    /**
     * Create an instance of Extras
     *
     * @param array|null $extras
     * @throws ExtraException
     */
    public function __construct(array $extras = null)
    {
        if ($extras) {
            foreach ($extras as $extra) {
                if (!is_int($extra) && !is_float($extra)) {
                    throw new ExtraException('Must provide a valid extra amount value');
                }
            }
        }


        $this->extras = new Collection($extras);
    }

    /**
     * Count the number of extras
     *
     * @return int
     */
    public function count(): int
    {
        return $this->extras->count();
    }

    /**
     * Get the sum of Extras amount
     *
     * @return float
     */
    public function amount(): float
    {
        return $this->extras->sum();
    }

    /**
     * Get the attributes of Extras as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->extras->toArray();
    }
}

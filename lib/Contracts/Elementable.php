<?php

namespace Beebmx\KirbyPay\Contracts;

interface Elementable
{
    /**
     * Get the attributes of elements as array
     * @return array
     */
    public function toArray(): array;
}

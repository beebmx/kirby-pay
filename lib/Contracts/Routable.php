<?php

namespace Beebmx\KirbyPay\Contracts;

interface Routable
{
    /**
     * Get all the routes for given route
     *
     * @return array
     */
    public static function all(): array;
}

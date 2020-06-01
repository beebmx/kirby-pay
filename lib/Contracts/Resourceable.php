<?php

namespace Beebmx\KirbyPay\Contracts;

interface Resourceable
{
    /**
     * Boot the resoruce
     *
     * @return void
     */
    public function boot();

    /**
     * Get the url of the service driver
     *
     * @return string
     */
    public static function serviceUrl(): string;
}

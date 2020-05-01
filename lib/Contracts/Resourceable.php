<?php


namespace Beebmx\KirbyPay\Contracts;


interface Resourceable
{
    public function boot();

    public static function serviceUrl();
}
<?php

namespace Beebmx\KirbyPay\Contracts;

use Illuminate\Support\Collection;

interface Driverable
{
    public function boot();

    public function createCustomer(Collection $customer, string $token, string $payment_method = null);

    public function parseCustomer(Collection $customer): array;

    public function createOrder(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null);

    public function createCharge(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null);

    public function parseOrder(Collection $order, Collection $customer = null, Collection $items = null): array;

    public function preparePrice($amount): int;

    public function parsePrice($amount): float;

}

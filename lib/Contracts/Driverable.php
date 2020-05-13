<?php

namespace Beebmx\KirbyPay\Contracts;

use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Shipping;
use Illuminate\Support\Collection;

interface Driverable
{
    public function boot();

    public function createCustomer(Collection $customer, string $token, string $payment_method = null);

    public function parseCustomer(Collection $customer): array;

    public function createOrder(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null);

    public function createCharge(Buyer $customer, Items $items, string $token = null, string $type = null, Shipping $shipping = null): Charge;

    //public function parseOrder(Collection $order, Collection $customer = null, Collection $items = null): array;

    public function preparePrice($amount): int;

    public function parsePrice($amount): float;

    public function getUrls(): array;

    public function getPaymentMethods(): array;
}

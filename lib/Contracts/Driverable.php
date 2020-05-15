<?php

namespace Beebmx\KirbyPay\Contracts;

use Beebmx\KirbyPay\Customer as ResourceCustomer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Customer;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order;
use Beebmx\KirbyPay\Elements\Shipping;

interface Driverable
{
    public function boot();

    public function getPaymentMethods(): array;

    public function getUrls(): array;

    public function createCustomer(Buyer $customer, string $token, string $payment_method = null): Customer;

    public function createOrder(ResourceCustomer $customer, Items $items, string $type = null, Shipping $shipping = null): Order;

    public function createCharge(Buyer $customer, Items $items, string $token = null, string $type = null, Shipping $shipping = null): Charge;

    public function preparePrice($amount): int;

    public function parsePrice($amount): float;
}

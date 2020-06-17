<?php

namespace Beebmx\KirbyPay\Contracts;

use Beebmx\KirbyPay\Customer as ResourceCustomer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Customer;
use Beebmx\KirbyPay\Elements\Extras;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order;
use Beebmx\KirbyPay\Elements\Shipping;
use Beebmx\KirbyPay\Elements\Source;

interface Driverable
{
    /**
     * Boot service driver
     *
     * @return void
     */
    public function boot();

    /**
     * Get payment methods available
     *
     * @return array
     */
    public function getPaymentMethods(): array;

    /**
     * Get urls for service driver
     *
     * @return array
     */
    public function getUrls(): array;

    /**
     * Create customer service driver
     *
     * @param Buyer $customer
     * @param string $token
     * @param string|null $payment_method
     * @return Customer
     */
    public function createCustomer(Buyer $customer, string $token, string $payment_method = null): Customer;

    /**
     * Update customer service driver
     *
     * @param ResourceCustomer $customer
     * @return bool
     */
    public function updateCustomer(ResourceCustomer $customer): bool;

    /**
     * Update customer payment source
     *
     * @param ResourceCustomer $customer
     * @param string $token
     * @return Source
     */
    public function updateCustomerSource(ResourceCustomer $customer, string $token): Source;

    /**
     * Delete customer service driver
     *
     * @param ResourceCustomer $customer
     * @return bool
     */
    public function deleteCustomer(ResourceCustomer $customer): bool;

    /**
     * Create Order service driver
     *
     * @param ResourceCustomer $customer
     * @param Items $items
     * @param Extras|null $extras
     * @param string|null $type
     * @param Shipping|null $shipping
     * @return Order
     */
    public function createOrder(ResourceCustomer $customer, Items $items, Extras $extras = null, string $type = null, Shipping $shipping = null): Order;

    /**
     * Create Charge service driver
     *
     * @param Buyer $customer
     * @param Items $items
     * @param Extras $extras
     * @param string|null $token
     * @param string|null $type
     * @param Shipping|null $shipping
     * @return Charge
     */
    public function createCharge(Buyer $customer, Items $items, Extras $extras = null, string $token = null, string $type = null, Shipping $shipping = null): Charge;

    /**
     * Prepare amount for service driver
     *
     * @param $amount
     * @return int
     */
    public function preparePrice($amount): int;

    /**
     * Parse amount with service driver unit
     *
     * @param $amount
     * @return float
     */
    public function parsePrice($amount): float;
}

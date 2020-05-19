<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Customer as ResourceCustomer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Customer;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order;
use Beebmx\KirbyPay\Elements\Shipping;
use Beebmx\KirbyPay\Elements\Source;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SandboxDriver extends Driver
{
    protected $secret = 'fake_secret_key';

    protected $version = '1.0.0';

    public function boot()
    {
    }

    protected $payment_methods = [
        'card',
    ];

    public function getUrls(): array
    {
        return [
            'customers' => '',
            'payments' => '',
            'logs' => '',
        ];
    }

    public function createCustomer(Buyer $customer, string $token, string $payment_method = null): Customer
    {
        $remoteCustomer = $this->simulateCustomer($customer, $payment_method)
                        ->only(['id', 'sources'])
                        ->toArray();

        $customer->id = $remoteCustomer['id'];

        return new Customer(
            $customer->id,
            $customer->email,
            $customer,
            new Source(
                $remoteCustomer['sources']['data'][0]['id'],
                $remoteCustomer['sources']['data'][0]['name'],
                $remoteCustomer['sources']['data'][0]['last4'],
                $remoteCustomer['sources']['data'][0]['type'],
                $remoteCustomer['sources']['data'][0]['brand'],
            ),
        );
    }

    protected function simulateCustomer(Buyer $customer, $payment_method)
    {
        return new Collection([
            'id' => 'cus_' . Str::random(20),
            'sources' => [
                'total' => 1,
                'data' => [[
                    'id' => 'src_' . Str::random(20),
                    'name' => $customer->name,
                    'exp_month' => 12,
                    'exp_year' => 19,
                    'type' => 'card',
                    'last4' => '4242',
                    'brand' => 'visa',
                ]]
            ],
        ]);
    }

    public function createOrder(ResourceCustomer $customer, Items $items, string $type = null, Shipping $shipping = null): Order
    {
        $buyer = new Buyer(
            $customer->customer['name'],
            $customer->customer['email'],
            $customer->customer['phone'],
            $customer->customer['id'],
        );

        $options = [
            'type' => $type,
            'payment_source_id' => $customer->source['id']
        ];

        $order = $this->simulateCharge($options, $buyer, $items, $type, $shipping)
            ->only(['id', 'payment_status'])
            ->toArray();

        return new Order(
            $order['id'],
            $order['payment_status'],
            $buyer,
            $items,
            $shipping
        );
    }

    public function createCharge(Buyer $customer, Items $items, string $token = null, string $type = null, Shipping $shipping = null): Charge
    {
        $charge = $this->simulateCharge([], $customer, $items, $type, $shipping)
                        ->only(['id', 'payment_status'])
                        ->toArray();

        return new Charge(
            $charge['id'],
            $charge['payment_status'],
            $customer,
            $items,
            $shipping
        );
    }

    protected function simulateCharge(array $options, Buyer $customer, Items $items, string $type = null, Shipping $shipping = null)
    {
        return new Collection(
            array_merge([
                'id' => 'ord_' . Str::random(20),
                'amount' => $this->preparePrice($items->amount()),
                'payment_status' => 'paid',
                'currency' => 'MXN',
                'customer_info' => [
                    'name' => $customer->name,
                    'email' => $customer->email,
                ],
                'line_items' => [
                    'total' => $items->count(),
                    'data' => $items->all()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'unit_price' => $this->preparePrice($item->amount),
                            'quantity' => $item->quantity,
                        ];
                    })->toArray(),
                ],
            ], $options, $shipping ? ['shipping' => $shipping->toArray()] : [])
        );
    }
}

<?php

namespace Beebmx\KirbyPay\Drivers;

use Faker\Factory as Faker;
use Illuminate\Support\Collection;

class SandboxDriver extends Driver
{
    protected $secret = 'fake_secret_key';

    protected $version = '1.0.0';

    protected $faker;

    public function boot()
    {
        $this->faker = Faker::create();
    }

    protected $payment_methods = [
        'card',
    ];

    public function getUrls(): array
    {
        return [
            'customers' => null,
            'payments' => null,
        ];
    }

    public function createCustomer(Collection $customer, string $token, string $payment_method = null)
    {
        if ($customer->isNotEmpty()) {
            return $this->parseCustomer($customer->merge([
                'livemode' => false,
                'id' => 'cus_2nZs3QNSTeqMWs1so',
                'object' => 'customer',
                'payment_sources' => [
                    'has_more' => false,
                    'object' => 'list',
                    'total' => 1,
                    'data' => [[
                        'id' => 'src_2fw8YeLSqoaGEYTn3',
                        'name' => $customer->get('name'),
                        'exp_month' => 12,
                        'exp_year' => 19,
                        'object' => 'payment_source',
                        'type' => 'card',
                        'created_at' => 1587597057,
                        'last4' => '4242',
                        'brand' => 'visa',
                        'parent_id' => 'cus_zzmjKsnM9oacyCwV3'
                    ]]
                ],
            ]));
        }

        return $this->parseCustomer(new Collection([
            'livemode' => false,
            'name' => $name = $this->faker->name,
            'email' => $this->faker->email,
            'id' => 'cus_2nZs3QNSTeqMWs1so',
            'object' => 'customer',
            'payment_sources' => [
                'has_more' => false,
                'object' => 'list',
                'total' => 1,
                'data' => [[
                    'id' => 'src_2fw8YeLSqoaGEYTn3',
                    'name' => $name,
                    'exp_month' => 12,
                    'exp_year' => 19,
                    'object' => 'payment_source',
                    'type' => 'card',
                    'created_at' => 1587597057,
                    'last4' => '4242',
                    'brand' => 'visa',
                    'parent_id' => 'cus_zzmjKsnM9oacyCwV3'
                ]]
            ],
        ]));
    }

    public function parseCustomer(Collection $customer): array
    {
        $toParse = $customer->only([
            'name', 'email', 'phone', 'id', 'payment_sources',
        ])->toArray();

        $payments = (new Collection(
            $toParse['payment_sources']['data']
        )
        )->map(function ($payment) {
            return [
                'name' => $payment['name'],
                'type' => $payment['type'],
                'last4' => $payment['last4'],
                'brand' => $payment['brand'],
            ];
        })->toArray();

        return [
            'name' => $toParse['name'],
            'email' => $toParse['email'],
            'phone' => $toParse['phone'] ?? null,
            'id' => $toParse['id'],
            'payments' => $payments,
        ];
    }

    public function createOrder(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null)
    {
        return $this->parseOrder(
            $this->orderWithItems($customer, $items, null, $type, $shipping)
        );
    }

    public function createCharge(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null)
    {
        return $this->parseOrder(
            $this->orderWithItems($customer, $items, $token, $type, $shipping)
        );
    }

    protected function orderWithItems(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null)
    {
        return new Collection(
            array_merge([
                'id' => 'ord_2fw8EWJusiRrxdPzT',
                'object' => 'order',
                'livemode' => false,
                'amount' => $items->sum(function($item) {
                    return $item['amount'] * $item['quantity'];
                }),
                'amount_refunded' => 0,
                'payment_status' => 'paid',
                'currency' => 'MXN',
                'customer_info' => [
                    'id' => $customer->get('id'),
                    'name' => $customer->get('name'),
                    'email' => $customer->get('email'),
                ],
                'created_at' => 1587597057,
                'updated_at' => 1587597057,
                'line_items' => [
                    'object' => 'list',
                    'has_more' => false,
                    'total' => $items->count(),
                    'data' => $items->map(function ($item) {
                        return [
                            'id' => 'line_item_2fw8EWJusiRrxdPzR',
                            'name' => $item['item'],
                            'unit_price' => $this->preparePrice($item['amount']),
                            'quantity' => $item['quantity'],
                        ];
                    })->toArray(),
                ],
                'charges' => [
                'object' => 'list',
                'has_more' => false,
                'total' => 1,
                'data' => [[
                    'id' => '589026bbedbb6e56430016ad',
                    'object' => 'charge',
                    'livemode' => false,
                    'created_at' => 1587597057,
                    'status' => 'paid',
                    'paid_at' => 1485842112,
                    'currency' => 'MXN',
                    'fee' => 1467,
                    'customer_id' => '',
                    'order_id' => 'ord_2fw8EWJusiRrxdPzT',
                    'payment_method' => [
                        'object' => 'card_payment',
                        'type' => 'credit',
                        'name' => 'Jorge Lopez',
                        'exp_month' => '12',
                        'exp_year' => '19',
                        'auth_code' => '490884',
                        'last4' => '4242',
                        'brand' => 'visa',
                        'issuer' => '',
                        'account_type' => '',
                        'country' => 'MX',
                        'fraud_score' => 29,
                        'fraud_indicators' => []
                    ]
                ]]
            ]
            ], $shipping ? $this->prepareShipping($shipping) : [])
        );
    }

    public function parseOrder(Collection $order, Collection $customer = null, Collection $items = null): array
    {
        $toParse = $order->only([
            'payment_status', 'currency', 'amount', 'id', 'line_items', 'customer_info', 'shipping_contacts'
        ])->toArray();

        if ($shipping = $toParse['shipping_contacts'] ?? null) {
            $shipping = $this->parseShipping(new Collection($toParse['shipping_contacts']['data']));
        }

        return array_merge([
            'status' => $toParse['payment_status'],
            'currency' => $toParse['currency'],
            'customer' => $toParse['customer_info'],
            'amount' => $toParse['amount'],
            'id' => $toParse['id'],
            'payment_id' => $toParse['id'],
            'items' => $this->parseItems(new Collection($toParse['line_items']['data'])),
        ], $shipping ?? []);
    }

    protected function prepareShipping(Collection $shipping)
    {
        if ($shipping->isNotEmpty()) {
            return [
                'shipping_contacts' => [
                    'has_more' => false,
                    'object' => "list",
                    'total' => 1,
                    'data' => [[
                        'id' => "ship_cont_afbur73vpdhdjwgo",
                        'object' => "shipping_contact",
                        'created_at' => 1587988905,
                        'address' => [
                            'object' => "shipping_address",
                            'street1' => $shipping->get('address'),
                            'city' => $shipping->get('city'),
                            'state' => $shipping->get('state'),
                            'postal_code' => $shipping->get('postal_code'),
                        ],
                    ]]
                ],
            ];
        }

        return [];
    }

    protected function parseItems(Collection $items)
    {
        return $items->map(function ($item) {
            return [
                'item' => $item['name'],
                'amount' => $this->parsePrice($item['unit_price']),
                'quantity' => $item['quantity'],
                'id' => $item['id'],
            ];
        })->toArray();
    }

    protected function parseShipping(Collection $shipping)
    {
        return ['shipping' => $shipping->map(function ($address) {
            return [
                'address' => $address['address']['street1'],
                'city' => $address['address']['city'],
                'state' => $address['address']['state'],
                'postal_code' => $address['address']['postal_code'],
            ];
        })->toArray()];
    }
}

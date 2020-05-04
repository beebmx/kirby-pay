<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\KirbyPay;
use Conekta\Conekta;
use Conekta\Customer;
use Conekta\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ConektaDriver extends Driver
{
    protected $version = '2.0.0';

    protected $payment_methods = [
        'card',
        'oxxo_cash'
    ];

    protected $unit = 100;

    public function boot()
    {
        Conekta::setApiKey($this->getSecret());
        Conekta::setApiVersion($this->version);
        Conekta::setLocale(KirbyPay::getLocaleCode());
    }

    public function getUrls(): array
    {
        return [
            'customers' => 'https://admin.conekta.com/customers',
            'payments' => 'https://admin.conekta.com/orders',
            'logs' => 'https://admin.conekta.com/logs',
        ];
    }

    public function createCustomer(Collection $customer, string $token, string $payment_method = null)
    {
        if ($token) {
            return $this->parseCustomer(
                new Collection(Customer::create(
                    $customer->merge([
                        'payment_sources' => [[
                            'token_id' => $token,
                            'type' => $payment_method
                        ]]
                    ])->toArray()
                ))
            );
        }

        return $this->parseCustomer(
            new Collection(Customer::create(
                $customer->toArray()
            ))
        );
    }

    public function parseCustomer(Collection $customer): array
    {
        $toParse = $customer->only([
            'id', 'name', 'email', 'phone', 'payment_sources', 'shipping_contacts',
        ])->toArray();

        $payments = (new Collection($toParse['payment_sources']))->only('data')->map(function ($item) {
            return (new Collection($item))->map(function ($payment) {
                return [
                    'id' => $payment['id'],
                    'type' => $payment['type'],
                    'last4' => $payment['last4'],
                    'brand' => $payment['brand'],
                    'name' => $payment['name'],
                    'customer_id' => $payment['parent_id'],
                ];
            });
        })->toArray()['data'];

        return [
            'name' => $toParse['name'],
            'email' => $toParse['email'],
            'phone' => $toParse['phone'] ?? null,
            'customer_id' => $toParse['id'],
            'id' => $toParse['id'],
            'payments' => $payments,
        ];
    }

    public function createOrder(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null)
    {
        return $this->parseOrder(
            new Collection($this->orderWithItems($customer, $items, null, $type, $shipping))
        );
    }

    public function createCharge(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null)
    {
        return $this->parseOrder(
            new Collection($this->orderWithItems($customer, $items, $token, $type, $shipping))
        );
    }

    protected function orderWithItems(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null)
    {
        $payment = [];
        if ($token) {
            $payment = [
                'token_id' => $token,
                'type' => $type
            ];
        } else {
            if ($type === 'oxxo_cash') {
                $payment = [
                    'type' => $type,
                    "expires_at" => Carbon::now()->addDays((int) pay('payment_expiration_days', 30))->timestamp
                ];
            } else {
                $payment = [
                    'type' => $type,
                    'payment_source_id' => (new Collection($customer->get('payments')))
                        ->filter(function ($payment) use ($type) {
                            return $payment['type'] === $type;
                        })->first()['id']
                ];
            }
        }

        return Order::create(
            array_merge([
                'currency' => strtoupper(pay('currency')),
                'customer_info' => $customer->filter(function ($value) {
                    return !empty($value);
                })->toArray(),
                'line_items' => $items->map(function ($item) {
                    return [
                        'name' => $item['item'],
                        'unit_price' => $this->preparePrice($item['amount']),
                        'quantity' => $item['quantity'],
                    ];
                })->toArray(),
                'charges' => [[
                    'payment_method' => $payment,
                ]],
            ], $shipping ? $this->prepareShipping($shipping) : [])
        );
    }

    public function parseOrder(Collection $order, Collection $customer = null, Collection $items = null): array
    {
        $toParse = $order->only([
            'id', 'amount', 'payment_status', 'currency', 'customer_info', 'line_items', 'charges', 'shipping_contact'
        ])->toArray();

        if ($shipping = $toParse['shipping_contact'] ?? null) {
            $shipping = $this->parseShipping(new Collection($toParse['shipping_contact']));
        }

        return array_merge([
            'status' => $toParse['payment_status'],
            'currency' => $toParse['currency'],
            'customer' => $toParse['customer_info'],
            'amount' => $this->parsePrice($toParse['amount']),
            'id' => $toParse['id'],
            'order_id' => $toParse['id'],
            'payment_id' => $toParse['id'],
            'charges' => $this->parseCharges(new Collection($toParse['charges'])),
            'items' => $this->parseItems(new Collection($toParse['line_items'])),
        ], $shipping ?? []);
    }

    protected function prepareShipping(Collection $shipping)
    {
        if ($shipping->isNotEmpty()) {
            $address = (new Collection([
                'street1' => $shipping->get('address'),
                'city' => $shipping->get('city'),
                'state' => $shipping->get('state'),
                'postal_code' => $shipping->get('postal_code'),
                'country' => $shipping->get('country'),
            ]))->filter(function ($value) {
                return !empty($value);
            })->toArray();

            return [
                'shipping_contact' => [
                    'address' => $address,
                ],
            ];
        }

        return [];
    }

    protected function parseItems(Collection $items)
    {
        return $items->only('data')->map(function ($lines) {
            return (new Collection($lines))->map(function ($item) {
                return [
                    'item' => $item['name'],
                    'amount' => $this->parsePrice($item['unit_price']),
                    'quantity' => $item['quantity'],
                    'id' => $item['id'],
                    'line_id' => $item['id'],
                ];
            });
        })->toArray()['data'];
    }

    protected function parseCharges(Collection $items)
    {
        return $items->only('data')->map(function ($lines) {
            return (new Collection($lines))->map(function ($item) {
                return [
                    'amount' => $this->parsePrice($item['amount']),
                    'created_at' => $item['created_at'],
                    'description' => $item['description'],
                    'fee' => $this->parsePrice($item['fee']),
                    'payment_method' => $item['payment_method']['store_name'] ?? null,
                    'barcode_url' => $item['payment_method']['barcode_url'] ?? null,
                    'reference' => $item['payment_method']['reference'] ?? null,
                    'service_name' => $item['payment_method']['service_name'] ?? null,
                    'type' => $item['payment_method']['type'] ?? null,
                    'expires_at' => $item['payment_method']['expires_at'] ?? null,
                ];
            });
        })->toArray()['data'];
    }

    protected function parseShipping(Collection $shipping)
    {
        return ['shipping' => [
            'address' => $shipping['address']['street1'],
            'city' => $shipping['address']['city'],
            'state' => $shipping['address']['state'],
            'postal_code' => $shipping['address']['postal_code'],
            'country' => strtoupper($shipping['address']['country']),
        ]];
    }
}

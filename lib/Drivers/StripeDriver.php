<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\KirbyPay;
use Illuminate\Support\Collection;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class StripeDriver extends Driver
{
    protected $version = '2020-03-02';

    protected $payment_methods = [
        'card',
    ];

    protected $unit = 100;

    public function boot()
    {
        Stripe::setApiKey($this->getSecret());
        Stripe::setAppInfo(
            'Kirby Pay',
            KirbyPay::VERSION,
            'https://github.com/beebmx/kirby-pay'
        );
        Stripe::setApiVersion($this->version);
    }

    public function getUrls(): array
    {
        if (pay('env', 'test') === 'test') {
            return [
                'customers' => 'https://dashboard.stripe.com/test/customers',
                'payments' => 'https://dashboard.stripe.com/test/payments',
            ];
        }

        return [
            'customers' => 'https://dashboard.stripe.com/customers',
            'payments' => 'https://dashboard.stripe.com/payments',
        ];
    }

    public function createCustomer(Collection $customer, string $token, string $payment_method = null)
    {
        if ($token) {
            return $this->parseCustomer(
                new Collection(Customer::create(
                    $customer->merge([
                        'source' =>  $token,
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
            'id', 'name', 'email', 'phone',  'sources', 'shipping',
        ])->toArray();

        $payments = (new Collection($toParse['sources']))->only('data')->map(function ($item) {
            return (new Collection($item))->map(function ($payment) {
                return [
                    'id' => $payment['id'],
                    'type' => $payment['object'],
                    'last4' => $payment['last4'],
                    'brand' => $payment['brand'],
                    'name' => $payment['name'],
                    'customer_id' => $payment['customer'],
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
            new Collection($this->orderWithItems($customer, $items, null, $type, $shipping)),
            $customer,
            $items
        );
    }

    public function createCharge(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null)
    {
        return $this->parseOrder(
            new Collection($this->orderWithItems($customer, $items, $token, $type, $shipping)),
            $customer,
            $items
        );
    }

    protected function orderWithItems(Collection $customer, Collection $items, string $token = null, string $type = null, Collection $shipping = null)
    {
        $options = [];
        if ($token) {
            if (strtolower($type) === 'card') {
                $options = [
                    'payment_method' => $this->getPaymentMethod($token),
                ];
            }
        }
        else {
            $options = [
                'customer' => $customer->get('id'),
            ];
        }

        return PaymentIntent::create(
            array_merge([
                'amount' => $this->getAmount($items),
                'currency' => strtoupper(pay('currency')),
                'confirm' => true,
                'confirmation_method' => 'automatic',
                'description' => pay('default_item_name'),
                'metadata' => array_merge([
                    'Real amount' => '$' . $this->parsePrice($this->getAmount($items)),
                    'Items' => $this->getCountItems($items),
                    'Total items' => $this->getTotalItems($items),
                ], $this->prepareItems($items)),
            ], $options, $shipping ? $this->prepareShipping($shipping, $customer) : [])
        );
    }

    public function parseOrder(Collection $order, Collection $customer = null, Collection $items = null): array
    {
        $toParse = $order->only([
            'id', 'amount', 'status', 'currency', 'customer', 'charges', 'object', 'shipping'
        ])->toArray();

        if ($shipping = $toParse['shipping'] ?? null) {
            $shipping = $this->parseShipping(new Collection($toParse['shipping']));
        }

        return array_merge([
            'status' => $toParse['status'],
            'currency' => $toParse['currency'],
            'customer' => $this->parseOrderCustomer($customer, $toParse['customer']),
            'amount' => $this->parsePrice($toParse['amount']),
            'id' => $toParse['id'],
            'payment_id' => $toParse['id'],
            'charges' => $toParse['charges'],
            'items' =>  $this->parseItems($items),
        ], $shipping ?? []);
    }

    protected function getAmount(Collection $items)
    {
        $amount = $items->sum(function($item) {
            return $item['amount'] * $item['quantity'];
        });

        return $this->preparePrice($amount);
    }

    protected function getCountItems(Collection $items)
    {
        return $items->count();
    }

    protected function getTotalItems(Collection $items)
    {
        return $items->sum(function($item) {
            return $item['quantity'];
        });
    }

    protected function getPaymentMethod(string $token)
    {
        return PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'token' => $token
            ],
        ]);
    }

    protected function parseOrderCustomer(Collection $customer, $id = null)
    {
        return $customer->filter(function($value){
            return !empty($value);
        })->merge(['id' => $id])->toArray();
    }

    protected function prepareItems(Collection $items)
    {
        return $items->mapWithKeys(function($item) {
            return ['Item: ' . $item['item'] => $item['quantity'] . ' x $' . $item['amount']];
        })->toArray();
    }

    protected function parseItems(Collection $items)
    {
        return $items->map(function($item) {
            return [
                'item' => $item['item'],
                'amount' => $item['amount'],
                'quantity' => $item['quantity'],
            ];
        })->toArray();
    }

    protected function prepareShipping(Collection $shipping, Collection $customer)
    {
        if ($shipping->isNotEmpty()) {
            $address = (new Collection([
                'line1' => $shipping->get('address'),
                'city' => $shipping->get('city'),
                'state' => $shipping->get('state'),
                'postal_code' => $shipping->get('postal_code'),
                'country' => $shipping->get('country'),
            ]))->filter(function ($value) {
                return !empty($value);
            })->toArray();

            return [
                'shipping' => [
                    'address' => $address,
                    'name' => $customer->get('name'),
                ],
            ];
        }

        return [];
    }

    protected function parseShipping(Collection $shipping)
    {
        return ['shipping' => [
            'address' => $shipping['address']['line1'],
            'city' => $shipping['address']['city'],
            'state' => $shipping['address']['state'],
            'postal_code' => $shipping['address']['postal_code'],
            'country' => strtoupper($shipping['address']['country']),
            'name' => $shipping['name'],
        ]];
    }
}

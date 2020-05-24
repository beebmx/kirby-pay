<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Customer as ResourceCustomer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Customer as ElementCustomer;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order as ElementOrder;
use Beebmx\KirbyPay\Elements\Shipping;
use Beebmx\KirbyPay\Elements\Source;
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

    public function createCustomer(Buyer $customer, string $token, string $payment_method = null): ElementCustomer
    {
        $remoteCustomer = (new Collection(
            Customer::create([
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'payment_sources' => [[
                    'token_id' => $token,
                    'type' => $payment_method,
                ]]
            ])
        ))->only(['id', 'payment_sources'])
          ->toArray();

        $customer->id = $remoteCustomer['id'];
        $customer->customer_id = $remoteCustomer['id'];

        return new ElementCustomer(
            $customer->id,
            $customer->email,
            $customer,
            new Source(
                $remoteCustomer['payment_sources'][0]['id'],
                $remoteCustomer['payment_sources'][0]['name'],
                $remoteCustomer['payment_sources'][0]['last4'],
                $remoteCustomer['payment_sources'][0]['type'],
                $remoteCustomer['payment_sources'][0]['brand'],
            ),
        );
    }

    public function updateCustomer(ResourceCustomer $customer): bool
    {
        return !!Customer::find($customer->id)
            ->update([
                'name' => $customer->customer['name'],
                'email' => $customer->customer['email'],
                'phone' => $customer->customer['phone'],
            ]);
    }

    public function deleteCustomer(ResourceCustomer $customer): bool
    {
        return !!Customer::find($customer->id)->delete();
    }

    public function updateCustomerSource(ResourceCustomer $customer, string $token): Source
    {
        $cus = Customer::find($customer->id);
        $cus->payment_sources[0]->delete();

        $source = $cus->createPaymentSource([
            'token_id' => $token,
            'type' => 'card'
        ]);

        return new Source(
            $source['id'],
            $source['name'],
            $source['last4'],
            $source['type'],
            $source['brand'],
        );
    }

    public function createOrder(ResourceCustomer $customer, Items $items, string $type = null, Shipping $shipping = null): ElementOrder
    {
        $buyer = new Buyer(
            $customer->customer['name'],
            $customer->customer['email'],
            $customer->customer['phone'],
            $customer->customer['id'],
        );

        if ($type === 'oxxo_cash') {
            $options = [
                'type' => 'oxxo_cash',
                'expires_at' => Carbon::now()->addDays((int) pay('payment_expiration_days', 30))->timestamp
            ];
        } else {
            $options = [
                'type' => $type,
                'payment_source_id' => $customer->source['id']
            ];
        }
        $order = $this->remoteOrder($options, $buyer, $items, $shipping)
                      ->only(['id', 'payment_status', 'charges'])
                      ->toArray();

        return new ElementOrder(
            $order['id'],
            $order['payment_status'],
            $buyer,
            $items,
            $shipping,
            $this->parseCharges(new Collection($order['charges']))
        );
    }

    public function createCharge(Buyer $customer, Items $items, string $token = null, string $type = null, Shipping $shipping = null): Charge
    {
        $options = [];
        if ($token) {
            $options = [
                'token_id' => $token,
                'type' => $type
            ];
        } elseif ($type === 'oxxo_cash') {
            $options = [
                'type' => 'oxxo_cash',
                'expires_at' => Carbon::now()->addDays((int) pay('payment_expiration_days', 30))->timestamp
            ];
        }

        $charge = $this->remoteOrder($options, $customer, $items, $shipping)
                       ->only(['id', 'payment_status', 'charges'])
                       ->toArray();

        return new Charge(
            $charge['id'],
            $charge['payment_status'],
            $customer,
            $items,
            $shipping,
            $this->parseCharges(new Collection($charge['charges']))
        );
    }

    protected function remoteOrder(array $options, Buyer $customer, Items $items, Shipping $shipping = null)
    {
        return new Collection(Order::create(
            array_merge([
                'currency' => strtoupper(pay('currency')),
                'customer_info' => (new Collection($customer))->filter(function ($value) {
                    return !empty($value);
                })->toArray(),
                'line_items' => $items->all()->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'unit_price' => $this->preparePrice($item->amount),
                        'quantity' => $item->quantity,
                    ];
                })->toArray(),
                'charges' => [[
                    'payment_method' => $options,
                ]],
            ], $shipping ? $this->prepareShipping($shipping) : [])
        ));
    }

    protected function prepareShipping(Shipping $shipping)
    {
        if ($shipping) {
            $address = (new Collection([
                'street1' => $shipping->address,
                'city' => $shipping->city,
                'state' => $shipping->state,
                'postal_code' => $shipping->postal_code,
                'country' => $shipping->country,
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
}

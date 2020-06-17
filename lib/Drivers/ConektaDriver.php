<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Customer as ResourceCustomer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Customer as ElementCustomer;
use Beebmx\KirbyPay\Elements\Extras;
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
use Illuminate\Support\Str;

class ConektaDriver extends Driver
{
    /**
     * Version of Conekta api
     *
     * @var string
     */
    protected $version = '2.0.0';

    /**
     * Payment methods available for Conekta
     *
     * @var array
     */
    protected $payment_methods = [
        'card',
        'oxxo_cash'
    ];

    /**
     * Unit in cents
     *
     * @var int
     */
    protected $unit = 100;

    /**
     * Initialize Conekta service driver
     *
     * @return void
     */
    public function boot()
    {
        Conekta::setApiKey($this->getSecret());
        Conekta::setApiVersion($this->version);
        Conekta::setLocale(KirbyPay::getLocaleCode());
    }

    /**
     * Get urls for Conekta service driver
     *
     * @return array
     */
    public function getUrls(): array
    {
        return [
            'customers' => 'https://admin.conekta.com/customers',
            'payments' => 'https://admin.conekta.com/orders',
            'logs' => 'https://admin.conekta.com/logs',
        ];
    }

    /**
     * Create Conekta customer
     *
     * @param Buyer $customer
     * @param string $token
     * @param string|null $payment_method
     * @return ElementCustomer
     */
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

    /**
     * Update Conekta customer
     *
     * @param ResourceCustomer $customer
     * @return bool
     */
    public function updateCustomer(ResourceCustomer $customer): bool
    {
        return !!Customer::find($customer->id)
            ->update([
                'name' => $customer->customer['name'],
                'email' => $customer->customer['email'],
                'phone' => $customer->customer['phone'],
            ]);
    }

    /**
     * Delete Conekta customer
     *
     * @param ResourceCustomer $customer
     * @return bool
     */
    public function deleteCustomer(ResourceCustomer $customer): bool
    {
        return !!Customer::find($customer->id)->delete();
    }

    /**
     * Update Conekta customer payment source
     *
     * @param ResourceCustomer $customer
     * @param string $token
     * @return Source
     */
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

    /**
     * Create Order Element and Conekta payment order
     *
     * @param ResourceCustomer $customer
     * @param Items $items
     * @param Extras|null $extras
     * @param string|null $type
     * @param Shipping|null $shipping
     * @return ElementOrder
     */
    public function createOrder(ResourceCustomer $customer, Items $items, Extras $extras = null, string $type = null, Shipping $shipping = null): ElementOrder
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
        $order = $this->remoteOrder($options, $buyer, $items, $extras, $shipping)
                      ->only(['id', 'payment_status', 'charges'])
                      ->toArray();

        return new ElementOrder(
            $order['id'],
            $order['payment_status'],
            $buyer,
            $items,
            $extras,
            $shipping,
            $this->parseCharges(new Collection($order['charges']))
        );
    }

    /**
     * Create Charge Element and Conekta order payment without Conekta customer
     *
     * @param Buyer $customer
     * @param Items $items
     * @param Extras|null $extras
     * @param string|null $token
     * @param string|null $type
     * @param Shipping|null $shipping
     * @return Charge
     */
    public function createCharge(Buyer $customer, Items $items, Extras $extras = null, string $token = null, string $type = null, Shipping $shipping = null): Charge
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

        $charge = $this->remoteOrder($options, $customer, $items, $extras, $shipping)
                       ->only(['id', 'payment_status', 'charges'])
                       ->toArray();

        return new Charge(
            $charge['id'],
            $charge['payment_status'],
            $customer,
            $items,
            $extras,
            $shipping,
            $this->parseCharges(new Collection($charge['charges']))
        );
    }

    /**
     * Create Conekta order
     *
     * @param array $options
     * @param Buyer $customer
     * @param Items $items
     * @param Extras|null $extras
     * @param Shipping|null $shipping
     * @return Collection
     */
    protected function remoteOrder(array $options, Buyer $customer, Items $items, Extras $extras = null, Shipping $shipping = null)
    {
        return new Collection(Order::create(
            array_merge([
                'currency' => strtoupper(pay('currency')),
                'customer_info' => (new Collection($customer))->filter(function ($value) {
                    return !empty($value);
                })->toArray(),
                'line_items' => array_merge($items->all()->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'unit_price' => $this->preparePrice($item->amount),
                        'quantity' => $item->quantity,
                    ];
                })->toArray(),
                    ($extras and $extras->count()) ? [[
                        'id' => Str::slug(pay('extra_amounts_item', 'Extra')),
                        'name' => pay('extra_amounts_item', 'Extra'),
                        'unit_price' => $this->preparePrice($extras->amount()),
                        'quantity' => 1,
                    ]] : []),
                'charges' => [[
                    'payment_method' => $options,
                ]],
            ], $shipping ? $this->prepareShipping($shipping) : [])
        ));
    }

    /**
     * Prepare shipping data through Shipping element
     *
     * @param Shipping $shipping
     * @return array
     */
    protected function prepareShipping(Shipping $shipping)
    {
        if ($shipping) {
            return [
                'shipping_contact' => [
                    'address' => (new Collection([
                        'street1' => $shipping->address,
                        'city' => $shipping->city,
                        'state' => $shipping->state,
                        'postal_code' => $shipping->postal_code,
                        'country' => $shipping->country,
                    ]))->filter(function ($value) {
                        return !empty($value);
                    })->toArray(),
                ],
            ];
        }

        return [];
    }

    /**
     * Parse charges for extra data
     *
     * @param Collection $items
     * @return array
     */
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

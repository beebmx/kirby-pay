<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Customer as ResourceCustomer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Customer;
use Beebmx\KirbyPay\Elements\Extras;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order;
use Beebmx\KirbyPay\Elements\Shipping;
use Beebmx\KirbyPay\Elements\Source;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SandboxDriver extends Driver
{
    /**
     * Simulate a secret key
     *
     * @var string
     */
    protected $secret = 'fake_secret_key';

    /**
     * Sandbox version
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * There is nothing to boot in sandbox
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Payment methods available for Sandbox
     *
     * @var array
     */
    protected $payment_methods = [
        'card',
    ];

    /**
     * There is not urls for Sandbox service driver
     *
     * @return array
     */
    public function getUrls(): array
    {
        return [
            'customers' => '',
            'payments' => '',
            'logs' => '',
        ];
    }

    /**
     * Create Sandbox customer
     *
     * @param Buyer $customer
     * @param string $token
     * @param string|null $payment_method
     * @return Customer
     */
    public function createCustomer(Buyer $customer, string $token, string $payment_method = null): Customer
    {
        $remoteCustomer = $this->simulateCustomer($customer)
                        ->only(['id', 'sources'])
                        ->toArray();

        $customer->id = $remoteCustomer['id'];
        $customer->customer_id = $remoteCustomer['id'];

        return new Customer(
            $customer->id,
            $customer->email,
            $customer,
            $this->createSource($remoteCustomer),
        );
    }

    /**
     * Simulate an update in Sandox customer service driver
     *
     * @param ResourceCustomer $customer
     * @return bool
     */
    public function updateCustomer(ResourceCustomer $customer): bool
    {
        return true;
    }

    /**
     * Simulate a delete in Sandox customer service driver
     *
     * @param ResourceCustomer $customer
     * @return bool
     */
    public function deleteCustomer(ResourceCustomer $customer): bool
    {
        return true;
    }

    /**
     * Simulate an update in Sandox customer source payment service driver
     *
     * @param ResourceCustomer $customer
     * @param string $token
     * @return Source
     */
    public function updateCustomerSource(ResourceCustomer $customer, string $token): Source
    {
        return $this->createSource([
            'sources' => $this->simulateSource(
                new Buyer(
                    $customer->customer['name'],
                    $customer->customer['email'],
                    $customer->customer['phone'],
                )
            )
        ]);
    }

    /**
     * Simulate a Customer in Sandbox service driver
     *
     * @param Buyer $customer
     * @return Collection
     */
    protected function simulateCustomer(Buyer $customer)
    {
        return new Collection([
            'id' => 'cus_' . Str::random(20),
            'sources' => $this->simulateSource($customer)
        ]);
    }

    /**
     * Simulate a customer payment source in Sandbox service driver
     *
     * @param Buyer $customer
     * @return array
     */
    protected function simulateSource(Buyer $customer): array
    {
        return [
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
        ];
    }

    /**
     * Create a Source payment in Sandbox service driver
     *
     * @param array $source
     * @return Source
     */
    protected function createSource(array $source): Source
    {
        return new Source(
            $source['sources']['data'][0]['id'],
            $source['sources']['data'][0]['name'],
            $source['sources']['data'][0]['last4'],
            $source['sources']['data'][0]['type'],
            $source['sources']['data'][0]['brand'],
        );
    }

    /**
     * Create an Order element in Sandbox service driver
     *
     * @param ResourceCustomer $customer
     * @param Items $items
     * @param Extras|null $extras
     * @param string|null $type
     * @param Shipping|null $shipping
     * @return Order
     */
    public function createOrder(ResourceCustomer $customer, Items $items, Extras $extras = null, string $type = null, Shipping $shipping = null): Order
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

        $order = $this->simulateCharge($options, $buyer, $items, $extras, $type, $shipping)
            ->only(['id', 'payment_status'])
            ->toArray();

        return new Order(
            $order['id'],
            $order['payment_status'],
            $buyer,
            $items,
            $extras,
            $shipping
        );
    }

    /**
     * Create a Charge element in Sandbox service driver
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
        $charge = $this->simulateCharge([], $customer, $items, $extras, $type, $shipping)
                        ->only(['id', 'payment_status'])
                        ->toArray();

        return new Charge(
            $charge['id'],
            $charge['payment_status'],
            $customer,
            $items,
            $extras,
            $shipping
        );
    }

    /**
     * Simulate a charge create in Sandbox service driver
     *
     * @param array $options
     * @param Buyer $customer
     * @param Items $items
     * @param Extras $extras
     * @param string|null $type
     * @param Shipping|null $shipping
     * @return Collection
     */
    protected function simulateCharge(array $options, Buyer $customer, Items $items, Extras $extras = null, string $type = null, Shipping $shipping = null)
    {
        return new Collection(
            array_merge([
                'id' => 'ord_' . Str::random(20),
                'amount' => $this->preparePrice(($extras and $extras->count()) ? $items->amount() + $extras->amount() : $items->amount()),
                'payment_status' => 'paid',
                'currency' => 'MXN',
                'customer_info' => [
                    'name' => $customer->name,
                    'email' => $customer->email,
                ],
                'line_items' => [
                    'total' => ($extras and $extras->count()) ? $items->count() + 1 : $items->count(),
                    'data' => array_merge($items->all()->map(function ($item) {
                        return [
                            'id' => $item->id,
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
                        ]] : []
                    ),
                ],
            ], $options, $shipping ? ['shipping' => $shipping->toArray()] : [])
        );
    }
}

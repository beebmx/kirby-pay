<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Customer as ResourceCustomer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Customer as ElementCustomer;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order;
use Beebmx\KirbyPay\Elements\Shipping;
use Beebmx\KirbyPay\Elements\Source;
use Beebmx\KirbyPay\KirbyPay;
use Illuminate\Support\Collection;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class StripeDriver extends Driver
{
    /**
     * Version of Stripe api
     *
     * @var string
     */
    protected $version = '2020-03-02';

    /**
     * Payment methods available for Stripe
     *
     * @var array
     */
    protected $payment_methods = [
        'card',
    ];

    /**
     * Unit in cents
     *
     * @var int
     */
    protected $unit = 100;

    /**
     * Initialize Stripe service driver
     *
     * @return void
     */
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

    /**
     * Get urls for Stripe service driver
     *
     * @return array
     */
    public function getUrls(): array
    {
        if (kpInDevelopment()) {
            return [
                'customers' => 'https://dashboard.stripe.com/test/customers',
                'payments' => 'https://dashboard.stripe.com/test/payments',
                'logs' => 'https://dashboard.stripe.com/test/logs',
            ];
        }

        return [
            'customers' => 'https://dashboard.stripe.com/customers',
            'payments' => 'https://dashboard.stripe.com/payments',
            'logs' => 'https://dashboard.stripe.com/logs',
        ];
    }

    /**
     * Create Stripe customer
     *
     * @param Buyer $customer
     * @param string $token
     * @param string|null $payment_method
     * @return ElementCustomer
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCustomer(Buyer $customer, string $token, string $payment_method = null): ElementCustomer
    {
        $remoteCustomer = (new Collection(
            Customer::create([
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'source' => $token
            ])
        ))->only(['id', 'sources'])
          ->toArray();

        $customer->id = $remoteCustomer['id'];
        $customer->customer_id = $remoteCustomer['id'];

        return new ElementCustomer(
            $customer->id,
            $customer->email,
            $customer,
            $this->createSource($remoteCustomer, $customer->name),
        );
    }

    /**
     * Update Stripe customer
     *
     * @param ResourceCustomer $customer
     * @return bool
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function updateCustomer(ResourceCustomer $customer): bool
    {
        return !!Customer::update(
            $customer->id,
            [
                'name' => $customer->customer['name'],
                'email' => $customer->customer['email'],
                'phone' => $customer->customer['phone'],
            ]
        );
    }

    /**
     * Delete Stripe customer
     *
     * @param ResourceCustomer $customer
     * @return bool
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function deleteCustomer(ResourceCustomer $customer): bool
    {
        return !!Customer::retrieve($customer->id)->delete();
    }

    /**
     * Update Stripe customer payment source
     *
     * @param ResourceCustomer $customer
     * @param string $token
     * @return Source
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function updateCustomerSource(ResourceCustomer $customer, string $token): Source
    {
        return $this->createSource(
            Customer::update(
                $customer->id,
                [
                    'source' => $token
                ]
            ),
            $customer->name
        );
    }

    /**
     * Create Stripe payment source
     *
     * @param $stripeCustomer
     * @param string $name
     * @return Source
     */
    protected function createSource($stripeCustomer, $name = ''): Source
    {
        return new Source(
            $stripeCustomer['sources']['data'][0]['id'],
            $stripeCustomer['sources']['data'][0]['name'] ?? $name,
            $stripeCustomer['sources']['data'][0]['last4'],
            $stripeCustomer['sources']['data'][0]['object'],
            $stripeCustomer['sources']['data'][0]['brand'],
        );
    }

    /**
     * Create Stripe payment order
     *
     * @param ResourceCustomer $customer
     * @param Items $items
     * @param string|null $type
     * @param Shipping|null $shipping
     * @return Order
     */
    public function createOrder(ResourceCustomer $customer, Items $items, string $type = null, Shipping $shipping = null): Order
    {
        $buyer = new Buyer(
            $customer->customer['name'],
            $customer->customer['email'],
            $customer->customer['phone'],
            $customer->customer['id'],
        );

        $options = [
            'customer' => $customer->id,
        ];

        $order = $this->remotePayment($options, $buyer, $items, $shipping)
                      ->only(['id', 'status', 'charges'])
                      ->toArray();

        return new Order(
            $order['id'],
            $order['status'],
            $buyer,
            $items,
            $shipping,
        );
    }

    /**
     * Create Stripe payment charge
     *
     * @param Buyer $customer
     * @param Items $items
     * @param string|null $token
     * @param string|null $type
     * @param Shipping|null $shipping
     * @return Charge
     */
    public function createCharge(Buyer $customer, Items $items, string $token = null, string $type = null, Shipping $shipping = null): Charge
    {
        $options = [
            'payment_method' => $this->getPaymentMethod($token),
        ];

        $charge = $this->remotePayment($options, $customer, $items, $shipping)
                       ->only(['id', 'status', 'charges'])
                       ->toArray();

        return new Charge(
            $charge['id'],
            $charge['status'],
            $customer,
            $items,
            $shipping,
        );
    }

    /**
     * Perform a Stripe Payment Intent
     *
     * @param array $options
     * @param Buyer $customer
     * @param Items $items
     * @param Shipping|null $shipping
     * @return Collection
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function remotePayment(array $options, Buyer $customer, Items $items, Shipping $shipping = null)
    {
        return new Collection(PaymentIntent::create(
            array_merge([
                'amount' => $this->preparePrice($items->amount()),
                'currency' => strtoupper(pay('currency')),
                'confirm' => true,
                'confirmation_method' => 'automatic',
                'description' => pay('default_item_name'),
                'metadata' => array_merge(
                    [
                        'Total amount' => '$' . $items->amount(),
                        'Items' => $items->count(),
                        'Total items' => $items->totalQuantity(),
                    ],
                    $items->all()->mapWithKeys(function ($item) {
                        return ['Item: ' . $item->name => $item->quantity . ' x $' . $item->amount];
                    })->toArray()
                ),
            ], $options, $shipping ? $this->prepareShipping($shipping, $customer) : [])
        ));
    }

    /**
     * Create Stripe Payment Method
     *
     * @param string $token
     * @return PaymentMethod
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function getPaymentMethod(string $token)
    {
        return PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'token' => $token
            ],
        ]);
    }

    /**
     * Prepare shipping data through Shipping and Buyer element
     *
     * @param Shipping $shipping
     * @param Buyer $customer
     * @return array
     */
    protected function prepareShipping(Shipping $shipping, Buyer $customer)
    {
        if ($shipping) {
            return [
                'shipping' => [
                    'address' => (new Collection([
                        'line1' => $shipping->address,
                        'city' => $shipping->city,
                        'state' => $shipping->state,
                        'postal_code' => $shipping->postal_code,
                        'country' => $shipping->country,
                    ]))->filter(function ($value) {
                        return !empty($value);
                    })->toArray(),
                    'name' => $customer->name,
                ],
            ];
        }

        return [];
    }
}

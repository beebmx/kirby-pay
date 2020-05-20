<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Customer as ResourceCustomer;
use Beebmx\KirbyPay\Drivers\SandboxDriver;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Customer;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order;
use Beebmx\KirbyPay\Elements\Shipping;
use Kirby\Cms\App;
use Kirby\Toolkit\Dir;

class DriverTest extends TestCase
{
    public $driver;

    public function setUp(): void
    {
        $this->driver = new class extends SandboxDriver {
            protected $unit = 100;

            protected $payment_methods = [
                'card', 'money'
            ];

            public function getUrls(): array
            {
                return [
                    'customers' => 'https://foo.bar/customers',
                    'payments' => 'https://foo.bar/payments',
                    'logs' => 'https://foo.bar/logs',
                ];
            }
        };

        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ]
        ]);
    }

    public function tearDown(): void
    {
        Dir::remove(__DIR__ . '/tmp/customer');
    }

    /** @test */
    public function it_returns_an_array_with_all_the_payment_methods_available()
    {
        $this->assertIsArray($this->driver->getPaymentMethods());
        $this->assertEquals('card', $this->driver->getPaymentMethods()[0]);
        $this->assertEquals('money', $this->driver->getPaymentMethods()[1]);
    }

    /** @test */
    public function it_returns_an_array_with_all_the_urls_available()
    {
        $this->assertIsArray($this->driver->getUrls());
        $this->assertArrayHasKey('customers', $this->driver->getUrls());
        $this->assertArrayHasKey('payments', $this->driver->getUrls());
        $this->assertArrayHasKey('logs', $this->driver->getUrls());
    }

    /** @test */
    public function it_prepares_a_number_into_a_driver_amount_format()
    {
        $this->assertEquals(9900, $this->driver->preparePrice(99.00));
    }

    /** @test */
    public function it_parse_an_amount_driver_number_into_normal_number()
    {
        $this->assertEquals(99.00, $this->driver->parsePrice(9900));
        $this->assertEquals(99, $this->driver->parsePrice(9900));
        $this->assertSame(99.00, $this->driver->parsePrice(9900));
    }

    /** @test */
    public function it_creates_a_customer_an_returns_an_customer_element()
    {
        $buyer = new Buyer(
            'John Doe',
            'john@mail.com',
            '1122334455'
        );

        $customer = $this->driver->createCustomer($buyer, 'token');

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertObjectHasAttribute('id', $customer);
        $this->assertObjectHasAttribute('email', $customer);
        $this->assertObjectHasAttribute('buyer', $customer);
        $this->assertObjectHasAttribute('source', $customer);
        $this->assertEquals($buyer->email, $customer->email);
    }

    /** @test */
    public function it_creates_an_order_and_returns_an_order_element()
    {
        $buyer = new Buyer(
            'Jane Doe',
            'jane@mail.com',
            '1122334455'
        );

        $customer = ResourceCustomer::create($buyer, 'token');

        $items = new Items([
            new Item('Product 01', 100, 1, 'product-01')
        ]);

        $order = $this->driver->createOrder(
            $customer,
            $items
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertObjectHasAttribute('id', $order);
        $this->assertObjectHasAttribute('currency', $order);
        $this->assertObjectHasAttribute('amount', $order);
        $this->assertObjectHasAttribute('status', $order);
        $this->assertObjectHasAttribute('customer', $order);
        $this->assertObjectHasAttribute('items', $order);
        $this->assertEquals($buyer->email, $order->customer->email);
    }

    /** @test */
    public function it_creates_a_charge_and_returns_a_charge_element()
    {
        $buyer = new Buyer(
            'Jane Doe',
            'jane@mail.com',
            '1122334455'
        );

        $items = new Items([
            new Item('Product 01', 100, 1, 'product-01')
        ]);

        $charge = $this->driver->createCharge(
            $buyer,
            $items
        );

        $this->assertInstanceOf(Charge::class, $charge);
        $this->assertObjectHasAttribute('id', $charge);
        $this->assertObjectHasAttribute('currency', $charge);
        $this->assertObjectHasAttribute('amount', $charge);
        $this->assertObjectHasAttribute('status', $charge);
        $this->assertObjectHasAttribute('customer', $charge);
        $this->assertObjectHasAttribute('items', $charge);
        $this->assertEquals($buyer->email, $charge->customer->email);
    }

    /** @test */
    public function it_creates_an_order_with_shipping_and_returns_an_order_element()
    {
        $buyer = new Buyer(
            'Jane Doe',
            'jane@mail.com',
            '1122334455'
        );

        $customer = ResourceCustomer::create($buyer, 'token');

        $items = new Items([
            new Item('Product 01', 100, 1, 'product-01')
        ]);

        $shipping = new Shipping(
            'Know address 123',
            '12345',
            'City',
            'State',
            'US'
        );

        $order = $this->driver->createOrder(
            $customer,
            $items,
            'card',
            $shipping
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertObjectHasAttribute('shipping', $order);
        $this->assertEquals($shipping, $order->shipping);
    }

    /** @test */
    public function it_creates_a_charge_with_shipping_and_returns_a_charge_element()
    {
        $buyer = new Buyer(
            'Jane Doe',
            'jane@mail.com',
            '1122334455'
        );

        $items = new Items([
            new Item('Product 01', 100, 1, 'product-01')
        ]);

        $shipping = new Shipping(
            'Know address 123',
            '12345',
            'City',
            'State',
            'US'
        );

        $charge = $this->driver->createCharge(
            $buyer,
            $items,
            'token',
            'card',
            $shipping
        );

        $this->assertInstanceOf(Charge::class, $charge);
        $this->assertObjectHasAttribute('shipping', $charge);
        $this->assertEquals($shipping, $charge->shipping);
    }
}

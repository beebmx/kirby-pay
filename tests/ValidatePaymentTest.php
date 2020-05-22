<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Routes\Routes;
use Kirby\Cms\App;
use Kirby\Http\Request;
use Kirby\Toolkit\Dir;

class ValidatePaymentTest extends TestCase
{
    protected $kirby;

    protected $session;

    public function setUp(): void
    {
        $this->kirby = new App([
            'roots' => [
                'index' => __DIR__ . '/tmp'
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ],
        ]);

        $this->session = $this->kirby->session();
    }

    public function tearDown(): void
    {
        $this->session->destroy();
        Dir::remove(__DIR__ . '/tmp/site');
        Dir::remove(__DIR__ . '/tmp/payment');
        Dir::remove(__DIR__ . '/tmp/customer');
    }

    /** @test */
    public function a_request_require_a_csrf_token()
    {
        $response = Routes::handleCreatePayment(new Request);

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('csrf-token', $response['type']);
    }

    /** @test */
    public function a_request_require_a_token()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('token', $response['type']);
    }

    /** @test */
    public function a_request_require_a_type_payment_method()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('type', $response['type']);
    }

    /** @test */
    public function a_request_require_a_customer_object()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('customer', $response['type']);
    }

    /** @test */
    public function a_request_require_an_items_object()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [[]],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('items', $response['type']);
    }

    /** @test */
    public function a_request_require_a_shipping_object()
    {
        App::destroy();
        $this->kirby = new App([
            'roots' => [
                'index' => __DIR__ . '/tmp'
            ],
            'options' => [
                'beebmx.kirby-pay.shipping' => true,
            ]
        ]);
        $this->session = $this->kirby->session();

        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [[]],
                    'items' => [[]],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('shipping', $response['type']);
    }

    /** @test */
    public function a_request_require_a_valid_customer_name()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [[]],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('name', $response['errors']);
    }

    /** @test */
    public function a_request_require_a_valid_customer_email()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                    ],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayNotHasKey('name', $response['errors']);
        $this->assertArrayHasKey('email', $response['errors']);
    }

    /** @test */
    public function a_request_require_a_valid_customer_phone()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                    ],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayNotHasKey('name', $response['errors']);
        $this->assertArrayNotHasKey('email', $response['errors']);
        $this->assertArrayHasKey('phone', $response['errors']);
    }

    /** @test */
    public function a_request_require_a_valid_shipping_address()
    {
        App::destroy();
        $this->kirby = new App([
            'roots' => [
                'index' => __DIR__ . '/tmp'
            ],
            'options' => [
                'beebmx.kirby-pay.shipping' => true,
            ]
        ]);
        $this->session = $this->kirby->session();

        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                        'phone' => '1122334455',
                    ],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                    'shipping' => [[]],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayNotHasKey('name', $response['errors']);
        $this->assertArrayNotHasKey('email', $response['errors']);
        $this->assertArrayNotHasKey('phone', $response['errors']);
        $this->assertArrayHasKey('address', $response['errors']);
    }

    /** @test */
    public function a_request_require_a_valid_shipping_state()
    {
        App::destroy();
        $this->kirby = new App([
            'roots' => [
                'index' => __DIR__ . '/tmp'
            ],
            'options' => [
                'beebmx.kirby-pay.shipping' => true,
            ]
        ]);
        $this->session = $this->kirby->session();

        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                        'phone' => '1122334455',
                    ],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                    'shipping' => [
                        'address' => 'Know address 123',
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayNotHasKey('address', $response['errors']);
        $this->assertArrayHasKey('state', $response['errors']);
    }

    /** @test */
    public function a_request_require_a_valid_shipping_country()
    {
        App::destroy();
        $this->kirby = new App([
            'roots' => [
                'index' => __DIR__ . '/tmp'
            ],
            'options' => [
                'beebmx.kirby-pay.shipping' => true,
            ]
        ]);
        $this->session = $this->kirby->session();

        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                        'phone' => '1122334455',
                    ],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                    'shipping' => [
                        'address' => 'Know address 123',
                        'state' => 'State',
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayNotHasKey('address', $response['errors']);
        $this->assertArrayNotHasKey('state', $response['errors']);
        $this->assertArrayHasKey('country', $response['errors']);
    }

    /** @test */
    public function a_request_require_a_valid_shipping_postal_code()
    {
        App::destroy();
        $this->kirby = new App([
            'roots' => [
                'index' => __DIR__ . '/tmp'
            ],
            'options' => [
                'beebmx.kirby-pay.shipping' => true,
            ]
        ]);
        $this->session = $this->kirby->session();

        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                        'phone' => '1122334455',
                    ],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                    'shipping' => [
                        'address' => 'Know address 123',
                        'state' => 'State',
                        'country' => 'US',
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayNotHasKey('address', $response['errors']);
        $this->assertArrayNotHasKey('state', $response['errors']);
        $this->assertArrayNotHasKey('country', $response['errors']);
        $this->assertArrayHasKey('postal_code', $response['errors']);
    }

    /** @test */
    public function a_request_validate_items_structure()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                        'phone' => '3311223344',
                    ],
                    'items' => [[]],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('item', $response['errors']);
        $this->assertArrayHasKey('item', $response['errors']);
        $this->assertArrayHasKey('item', $response['errors']);
    }

    /** @test */
    public function a_request_validate_item_structure()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                        'phone' => '3311223344',
                    ],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                        ['amount' => 200.00, 'quantity' => 1],
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('item', $response['errors']);
        $this->assertArrayHasKey('item', $response['errors']);
        $this->assertArrayHasKey('item', $response['errors']);
    }

    /** @test */
    public function a_request_validate_and_create_a_payment()
    {
        $response = Routes::handleCreatePayment(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'type' => 'card',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                        'phone' => '3311223344',
                    ],
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                        ['name' => 'Product 002', 'amount' => 300.00, 'quantity' => 1],
                    ],
                ]
            ])
        );

        $this->assertTrue($response['success']);
        $this->assertFalse($response['error']);
    }
}

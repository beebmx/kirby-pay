<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Routes\Routes;
use Illuminate\Support\Str;
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
    public function a_request_for_payment_require_a_csrf_token()
    {
        $response = Routes::handleCreatePayment(new Request);

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('csrf-token', $response['type']);
    }

    /** @test */
    public function a_request_for_payment_require_a_token()
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
    public function a_request_for_payment_require_a_type_payment_method()
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
    public function a_request_for_payment_require_a_customer_object()
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
    public function a_request_for_payment_require_an_items_object()
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
    public function a_request_for_payment_require_a_shipping_object()
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
    public function a_request_for_payment_require_a_valid_customer_name()
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
    public function a_request_for_payment_require_a_valid_customer_email()
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
    public function a_request_for_payment_require_a_valid_customer_phone()
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
    public function a_request_for_payment_require_a_valid_shipping_address()
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
    public function a_request_for_payment_require_a_valid_shipping_state()
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
    public function a_request_for_payment_require_a_valid_shipping_country()
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
    public function a_request_for_payment_require_a_valid_shipping_postal_code()
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
    public function a_request_for_payment_validate_items_structure()
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
    public function a_request_for_payment_validate_item_structure()
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
    public function a_request_for_payment_validate_extra_amounts_structure()
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
                    ],
                    'extra_amounts' => [
                        'shipping' => 'invalid-data'
                    ]
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('shipping', $response['errors']);

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
                    ],
                    'extra_amounts' => [
                        'shipping' => 200,
                        'others' => 'invalid',
                    ]
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('others', $response['errors']);

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
                    ],
                    'extra_amounts' => [
                        'shipping' => 200,
                    ],
                ]
            ])
        );

        $this->assertTrue($response['success']);
        $this->assertFalse($response['error']);
    }

    /** @test */
    public function a_request_for_payment_validate_and_create_a_payment()
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

    /** @test */
    public function a_request_for_order_require_a_csrf_token()
    {
        $response = Routes::handleCreateOrder(new Request);

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('csrf-token', $response['type']);
    }

    /** @test */
    public function a_request_for_order_require_an_id()
    {
        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('id', $response['type']);
    }

    /** @test */
    public function a_request_for_order_require_a_valid_uuid()
    {
        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => 'invalid-uuid',
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('id', $response['type']);
    }

    /** @test */
    public function a_request_for_order_require_an_items_object()
    {
        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('items', $response['type']);
    }

    /** @test */
    public function a_request_for_order_require_a_shipping_object()
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

        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'items' => [[]],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('shipping', $response['type']);
    }

    /** @test */
    public function a_request_for_order_require_a_valid_shipping_address()
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

        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
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
    public function a_request_for_order_require_a_valid_shipping_state()
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

        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
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
    public function a_request_for_order_require_a_valid_shipping_country()
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

        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
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
    public function a_request_for_order_require_a_valid_shipping_postal_code()
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

        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
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
    public function a_request_for_order_validate_items_structure()
    {
        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
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
    public function a_request_for_order_validate_item_structure()
    {
        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
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
    public function a_request_for_order_validate_extra_amounts_structure()
    {
        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                    'extra_amounts' => [
                        'shipping' => 'invalid-data'
                    ]
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('shipping', $response['errors']);

        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                    'extra_amounts' => [
                        'shipping' => 200,
                        'others' => 'invalid',
                    ]
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('others', $response['errors']);

        $customer = Customer::create(
            new Buyer(
                'John Doe',
                'example@mail.com',
                '1122334455'
            ),
            'test-token'
        );
        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => $customer->uuid,
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                    ],
                    'extra_amounts' => [
                        'shipping' => 200,
                    ]
                ]
            ])
        );

        $this->assertTrue($response['success']);
        $this->assertFalse($response['error']);
    }

    /** @test */
    public function a_request_for_order_require_an_existing_customer()
    {
        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                        ['name' => 'Product 002', 'amount' => 300.00, 'quantity' => 1],
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals(kpT('validation.customer.not-found'), $response['errors']);
    }

    /** @test */
    public function a_request_for_order_validate_and_create_a_payment()
    {
        $this->assertCount(0, Payment::get());
        $customer = Customer::create(
            new Buyer(
                'John Doe',
                'example@mail.com',
                '1122334455'
            ),
            'test-token'
        );

        $response = Routes::handleCreateOrder(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => $customer->uuid,
                    'items' => [
                        ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1],
                        ['name' => 'Product 002', 'amount' => 300.00, 'quantity' => 1],
                    ],
                ]
            ])
        );

        $this->assertTrue($response['success']);
        $this->assertFalse($response['error']);
        $this->assertCount(1, Payment::get());
    }
}

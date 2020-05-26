<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Routes\Routes;
use Illuminate\Support\Str;
use Kirby\Cms\App;
use Kirby\Http\Request;
use Kirby\Toolkit\Dir;

class ValidateCustomerTest extends TestCase
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
        Dir::remove(__DIR__ . '/tmp/customer');
    }

    /** @test */
    public function a_request_for_create_customer_require_a_csrf_token()
    {
        $response = Routes::handleCreateCustomer(new Request);

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('csrf-token', $response['type']);
    }

    /** @test */
    public function a_request_for_create_customer_require_a_token()
    {
        $response = Routes::handleCreateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('token', $response['type']);
    }

    /** @test */
    public function a_request_for_create_customer_require_a_customer_object()
    {
        $response = Routes::handleCreateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('customer', $response['type']);
    }

    /** @test */
    public function a_request_for_create_customer_require_a_valid_customer_name()
    {
        $response = Routes::handleCreateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'customer' => [[]],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('name', $response['errors']);
    }

    /** @test */
    public function a_request_for_create_customer_require_a_valid_customer_email()
    {
        $response = Routes::handleCreateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'customer' => [
                        'name' => 'John Doe',
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
    public function a_r_for_create_customer_a_valid_customer_phone()
    {
        $response = Routes::handleCreateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
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
    public function a_request_for_create_customer_creates_a_valid_customer()
    {
        $this->assertCount(0, Customer::get());
        $response = Routes::handleCreateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'token' => 'sandbox-token',
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
                        'phone' => '1122334455',
                    ],
                ]
            ])
        );

        $this->assertCount(1, Customer::get());
        $this->assertTrue($response['success']);
        $this->assertFalse($response['error']);
    }

    /** @test */
    public function a_request_for_update_customer_require_a_csrf_token()
    {
        $response = Routes::handleUpdateCustomer(new Request);

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('csrf-token', $response['type']);
    }

    /** @test */
    public function a_request_for_update_customer_require_an_id()
    {
        $response = Routes::handleUpdateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('id', $response['type']);
    }

    /** @test */
    public function a_request_for_update_customer_require_a_valid_uuid()
    {
        $response = Routes::handleUpdateCustomer(
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
    public function a_request_for_update_customer_require_a_customer_object()
    {
        $response = Routes::handleUpdateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('customer', $response['type']);
    }

    /** @test */
    public function a_request_for_update_customer_require_a_valid_customer_name()
    {
        $response = Routes::handleUpdateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'customer' => [[]],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('name', $response['errors']);
    }

    /** @test */
    public function a_request_for_update_customer_require_a_valid_customer_email()
    {
        $response = Routes::handleUpdateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'customer' => [
                        'name' => 'John Doe',
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
    public function a_request_for_update_customer_a_valid_customer_phone()
    {
        $response = Routes::handleUpdateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'customer' => [
                        'name' => 'John Doe',
                        'email' => 'example@mail.com',
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
    public function a_request_for_update_customer_requires_an_existing_customer()
    {
        $response = Routes::handleUpdateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'customer' => [
                        'name' => 'Jane Doe',
                        'email' => 'jane@doe.com',
                        'phone' => '5544332211'
                    ],
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals(kpT('validation.customer.not-found'), $response['errors']);
    }

    /** @test */
    public function a_request_for_update_customer_updates_the_customer_resource()
    {
        $customer = Customer::create(
            new Buyer(
                'John Doe',
                'example@mail.com',
                '1122334455'
            ),
            'test-token'
        );

        $response = Routes::handleUpdateCustomer(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => $customer->uuid,
                    'customer' => [
                        'name' => 'Jane Doe',
                        'email' => 'jane@doe.com',
                        'phone' => '5544332211'
                    ],
                ]
            ])
        );

        $this->assertTrue($response['success']);
        $this->assertEquals('Jane Doe', Customer::first()->customer['name']);
        $this->assertEquals('jane@doe.com', Customer::first()->customer['email']);
        $this->assertEquals('5544332211', Customer::first()->customer['phone']);
    }

    /** @test */
    public function a_request_for_update_source_require_a_csrf_token()
    {
        $response = Routes::handleUpdateSource(new Request);

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('csrf-token', $response['type']);
    }

    /** @test */
    public function a_request_for_update_source_require_an_id()
    {
        $response = Routes::handleUpdateSource(
            new Request([
                'query' => ['csrf' => csrf()],
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('id', $response['type']);
    }

    /** @test */
    public function a_request_for_update_source_require_a_valid_uuid()
    {
        $response = Routes::handleUpdateSource(
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
    public function a_request_for_update_source_require_a_token()
    {
        $response = Routes::handleUpdateSource(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('token', $response['type']);
    }

    /** @test */
    public function a_request_for_update_source_require_an_existing_customer()
    {
        $response = Routes::handleUpdateSource(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => (string) Str::uuid(),
                    'token' => 'test-token'
                ]
            ])
        );

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals(kpT('validation.customer.not-found'), $response['errors']);
    }

    /** @test */
    public function a_request_for_update_source_updates_the_customer_resource()
    {
        $customer = Customer::create(
            new Buyer(
                'John Doe',
                'example@mail.com',
                '1122334455'
            ),
            'test-token'
        );

        $oldSourceId = $customer->source['id'];

        $response = Routes::handleUpdateSource(
            new Request([
                'query' => ['csrf' => csrf()],
                'body' => [
                    'id' => $customer->uuid,
                    'token' => 'test-token'

                ]
            ])
        );

        $this->assertTrue($response['success']);
        $this->assertNotEquals($oldSourceId, Customer::first()->source['id']);
    }
}

<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Routes\Routes;
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
    public function a_request_require_a_csrf_token()
    {
        $response = Routes::handleCreateCustomer(new Request);

        $this->assertFalse($response['success']);
        $this->assertTrue($response['error']);
        $this->assertEquals('csrf-token', $response['type']);
    }

    /** @test */
    public function a_request_require_a_token()
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
    public function a_request_require_a_customer_object()
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
    public function a_request_require_a_valid_customer_name()
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
    public function a_request_require_a_valid_customer_email()
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
    public function a_request_require_a_valid_customer_phone()
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
    public function a_request_creates_a_valid_customer()
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
}

<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Buyer;
use Kirby\Cms\App;
use Kirby\Toolkit\Dir;

class CustomerTest extends TestCase
{
    public $buyer;

    public function setUp(): void
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ]
        ]);

        $this->buyer = new Buyer('John Doe', 'example@email.com', '1122334455');
    }

    public function tearDown(): void
    {
        Dir::remove(__DIR__ . '/tmp/customer');
    }

    /** @test */
    public function it_creates_a_customer()
    {
        $this->assertTrue(Customer::isEmpty());
        Customer::create($this->buyer, 'token');

        $this->assertFalse(Customer::isEmpty());
        $this->assertEquals(1, Customer::count());
        $this->assertCount(1, Customer::get());
    }

    /** @test */
    public function it_creates_a_customer_with_data()
    {
        Customer::create($this->buyer, 'token');
        $this->assertEquals('example@email.com', Customer::first()->email);
    }

    /** @test */
    public function it_creates_a_customer_if_not_exists()
    {
        $this->assertTrue(Customer::isEmpty());
        Customer::firstOrCreate($this->buyer, 'token');

        $this->assertFalse(Customer::isEmpty());
        $this->assertEquals(1, Customer::count());
        $this->assertCount(1, Customer::get());
    }

    /** @test */
    public function it_creates_a_customer_only_if_not_exists()
    {
        Customer::create($this->buyer, 'token');
        Customer::firstOrCreate($this->buyer, 'token');

        $this->assertFalse(Customer::isEmpty());
        $this->assertEquals(1, Customer::count());
        $this->assertCount(1, Customer::get());
    }

    /** @test */
    public function it_returns_the_first_customer_with_an_email()
    {
        $customer = Customer::create($this->buyer, 'token');
        $this->assertEquals($customer, Customer::email('example@email.com'));
    }

    /** @test */
    public function it_returns_false_if_not_customer_with_email_was_found()
    {
        $customer = Customer::create($this->buyer, 'token');
        $this->assertFalse(Customer::email('demo@email.com'));
    }
}

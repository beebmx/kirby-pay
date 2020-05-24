<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Exception\UpdateCustomerException;
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

    /** @test */
    public function it_throw_an_exception_if_wants_to_update_a_not_initialized_customer()
    {
        $this->expectException(UpdateCustomerException::class);
        $customerNotIntDriverPlatform = new Customer();
        $customerNotIntDriverPlatform->update(new Buyer('John Doe', 'example@email.com', '1122334455'));
    }

    /** @test */
    public function it_updates_a_customer_name()
    {
        $customer = Customer::create($this->buyer, 'token');
        $this->assertEquals('John Doe', Customer::first()->customer['name']);

        $customer->update(new Buyer(
            'Jane Doe',
            $customer->customer['email'],
            $customer->customer['phone'],
        ));

        $this->assertEquals($customer->id, Customer::first()->id);
        $this->assertNotEquals('John Doe', Customer::first()->customer['name']);
        $this->assertEquals('Jane Doe', Customer::first()->customer['name']);
        $this->assertEquals($customer->customer['email'], Customer::first()->customer['email']);
        $this->assertEquals($customer->customer['phone'], Customer::first()->customer['phone']);
    }

    /** @test */
    public function it_updates_a_customer_email()
    {
        $customer = Customer::create($this->buyer, 'token');
        $this->assertEquals('example@email.com', Customer::first()->customer['email']);

        $customer->update(new Buyer(
            $customer->customer['name'],
            'john@doe.com',
            $customer->customer['phone'],
        ));

        $this->assertNotEquals('example@email.com', Customer::first()->customer['name']);
        $this->assertEquals('john@doe.com', Customer::first()->customer['email']);
        $this->assertEquals($customer->customer['name'], Customer::first()->customer['name']);
        $this->assertEquals($customer->customer['phone'], Customer::first()->customer['phone']);
    }

    /** @test */
    public function it_updates_a_customer_phone()
    {
        $customer = Customer::create($this->buyer, 'token');
        $this->assertEquals('1122334455', Customer::first()->customer['phone']);

        $customer->update(new Buyer(
            $customer->customer['name'],
            $customer->customer['email'],
            '5544332211',
        ));

        $this->assertEquals($customer->id, Customer::first()->id);
        $this->assertNotEquals('1122334455', Customer::first()->customer['phone']);
        $this->assertEquals('5544332211', Customer::first()->customer['phone']);
        $this->assertEquals($customer->customer['name'], Customer::first()->customer['name']);
        $this->assertEquals($customer->customer['email'], Customer::first()->customer['email']);
    }

    /** @test */
    public function it_throw_an_exception_if_wants_to_update_a_customer_source_with_not_initialized_customer()
    {
        $this->expectException(UpdateCustomerException::class);
        $customerNotIntDriverPlatform = new Customer();
        $customerNotIntDriverPlatform->updateSource('new-token');
    }

    /** @test */
    public function it_updates_a_customer_source()
    {
        $customer = Customer::create($this->buyer, 'token');
        $oldSourceId = $customer->source['id'];
        $this->assertEquals($customer->source['id'], Customer::first()->source['id']);
        $this->assertEquals($customer->source['id'], Customer::first()->source['id']);

        $customer->updateSource('new-token');

        $this->assertEquals($customer->id, Customer::first()->id);
        $this->assertEquals($customer->source['id'], Customer::first()->source['id']);
        $this->assertNotEquals($oldSourceId, Customer::first()->source['id']);
    }

    /** @test */
    public function it_throw_an_exception_if_wants_to_delete_a_customer_not_initialized()
    {
        $this->expectException(UpdateCustomerException::class);
        $customerNotIntDriverPlatform = new Customer();
        $customerNotIntDriverPlatform->remove();
    }

    /** @test */
    public function it_remove_a_customer_from_resource_and_driver()
    {
        $customer = Customer::create($this->buyer, 'token');
        $this->assertCount(1, Customer::get());

        $customer->remove();
        $this->assertCount(0, Customer::get());
    }
}

<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Drivers\ConektaDriver;
use Beebmx\KirbyPay\Drivers\Factory;
use Beebmx\KirbyPay\Drivers\SandboxDriver;
use Beebmx\KirbyPay\Drivers\StripeDriver;
use Exception;
use Kirby\Cms\App as Kirby;
use PHPUnit\Framework\TestCase;

class DriverTest extends TestCase
{
    protected function setUp(): void
    {
        Kirby::destroy();
    }
    /** @test */
    public function it_throw_an_error_if_the_driver_does_not_exists()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The driver requested does not exists');

        Kirby::plugin('beebmx/kirby-pay', [
            'options' => [
                'service' => 'invalid',
                'keys' => [
                    'secret' => 'my-secret-key'
                ]
            ]
        ]);

        new Kirby;

        (new Factory)->find();
    }

    /** @test */
    public function it_validates_demo_driver_exists()
    {
        $this->assertInstanceOf(SandboxDriver::class, (new Factory)->find());
    }

    /** @test */
    public function it_validates_conekta_driver_exists()
    {

        Kirby::plugin('beebmx/kirby-pay', [
            'options' => [
                'service' => 'conekta',
                'service_secret' => 'conekta_secret_key',
            ]
        ]);
        new Kirby;

        $this->assertInstanceOf(ConektaDriver::class, (new Factory)->find());
    }

    /** @test */
    public function it_validates_stripe_driver_exists()
    {

        Kirby::plugin('beebmx/kirby-pay', [
            'options' => [
                'service' => 'stripe',
                'service_secret' => 'stripe_secret_key',
            ]
        ]);
        new Kirby;

        $this->assertInstanceOf(StripeDriver::class, (new Factory)->find());
    }
}

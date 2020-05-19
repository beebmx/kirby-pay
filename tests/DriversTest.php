<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Drivers\ConektaDriver;
use Beebmx\KirbyPay\Drivers\Factory;
use Beebmx\KirbyPay\Drivers\SandboxDriver;
use Beebmx\KirbyPay\Drivers\StripeDriver;
use Exception;
use Kirby\Cms\App;

class DriversTest extends TestCase
{
    /** @test */
    public function it_throw_an_error_if_the_driver_does_not_exists()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The driver requested does not exists');

        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.service' => 'invalid',
                'beebmx.kirby-pay.service_secret' => 'demo-token',
            ]
        ]);

        (new Factory)->find();
    }

    /** @test */
    public function it_validates_sandbox_driver_exists()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ]
        ]);

        $this->assertInstanceOf(SandboxDriver::class, (new Factory)->find());
    }

    /** @test */
    public function it_validates_conekta_driver_exists()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.service' => 'conekta',
                'beebmx.kirby-pay.service_secret' => 'demo-token',
            ]
        ]);

        $this->assertInstanceOf(ConektaDriver::class, (new Factory)->find());
    }

    /** @test */
    public function it_validates_stripe_driver_exists()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.service' => 'stripe',
                'beebmx.kirby-pay.service_secret' => 'demo-token',
            ]
        ]);

        $this->assertInstanceOf(StripeDriver::class, (new Factory)->find());
    }
}

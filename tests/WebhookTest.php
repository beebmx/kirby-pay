<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Log;
use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Webhook;
use Illuminate\Support\Collection;
use Kirby\Cms\App;
use Kirby\Http\Request;
use Kirby\Toolkit\Dir;

class WebhookTest extends TestCase
{
    public $payment;

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

        $this->payment = Payment::charge(
            new Collection([
                'name' => 'John Doe',
                'email' => 'example@email.com',
                'phone' => '1122334455'
            ]),
            new Collection([
                ['id' => 'product-01', 'name' => 'Product 01', 'amount' => 100, 'quantity' => 1],
                ['id' => 'product-02', 'name' => 'Product 02', 'amount' => 100, 'quantity' => 1],
            ]),
            null,
            'token'
        );
    }

    public function tearDown(): void
    {
        Dir::remove(__DIR__ . '/tmp/payment');
        Dir::remove(__DIR__ . '/tmp/logs');
    }

    /** @test */
    public function a_webhook_find_the_same_payment_requested()
    {
        $webhook = new Webhook(
            new Request(['body' => [
                'id' => $this->payment->id,
                'type' => 'charge.created',
            ]])
        );

        $payment = $webhook->handleChargeCreated();

        $this->assertEquals($this->payment->id, $payment->id);
        $this->assertEquals($this->payment->uuid, $payment->uuid);
    }

    /** @test */
    public function a_webhook_change_the_status_of_the_payment()
    {
        $webhook = new Webhook(
            new Request(['body' => [
                'id' => $this->payment->id,
                'type' => 'charge.expired',
                'status' => 'expired',
            ]])
        );
        $this->assertEquals('paid', Payment::first()->status);

        $payment = $webhook->handleChargeExpired();

        $this->assertEquals($this->payment->id, $payment->id);
        $this->assertEquals($this->payment->uuid, $payment->uuid);
        $this->assertEquals('expired', Payment::first()->status);
    }

    /** @test */
    public function a_webhook_ignore_all_logs()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
                'beebmx.kirby-pay.logs' => false,
            ]
        ]);

        $webhook = new Webhook(
            new Request(['body' => [
                'id' => $this->payment->id,
                'type' => 'charge.expired',
                'status' => 'expired',
            ]])
        );
        $webhook->handle();

        $this->assertTrue(Log::isEmpty());
        $this->assertCount(0, Log::get());
    }

    /** @test */
    public function a_webhook_store_all_logs()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
                'beebmx.kirby-pay.logs' => true,
            ]
        ]);

        $webhook = new Webhook(
            new Request(['body' => [
                'id' => $this->payment->id,
                'type' => 'charge.expired',
                'status' => 'expired',
            ]])
        );
        $webhook->handle();

        $this->assertFalse(Log::isEmpty());
        $this->assertCount(1, Log::get());
        $this->assertEquals(Log::first()->id, $this->payment->id);
    }
}

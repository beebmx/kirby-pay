<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Webhook;
use Illuminate\Support\Collection;
use Kirby\Cms\App;
use Kirby\Http\Request;
use Kirby\Toolkit\Dir;

class HookTest extends TestCase
{
    /** @test */
    public function a_webhook_trigger_a_hook()
    {
        $phpunit = $this;

        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ],
            'hooks' => [
                'beebmx.kirby-pay.test.webhook' => function (Payment $payment) use ($phpunit) {
                    $phpunit->assertNotNull($payment);
                    $phpunit->assertNotNull($payment->id);
                    $phpunit->assertStringStartsWith('ord_', $payment->id);
                }
            ]
        ]);

        $payment = Payment::charge(
            new Collection([
                'name' => 'John Doe',
                'email' => 'example@email.com',
                'phone' => '1122334455'
            ]),
            new Collection([
                ['id' => 'product-01', 'name' => 'Product 01', 'amount' => 100, 'quantity' => 1],
                ['id' => 'product-02', 'name' => 'Product 02', 'amount' => 100, 'quantity' => 1],
            ]),
            'token'
        );

        $webhook = new Webhook(
            new Request(['body' => [
                'id' => $payment->id,
                'type' => 'test.webhook',
                'status' => 'expired',
            ]])
        );
        $webhook->handle();

        Dir::remove(__DIR__ . '/tmp/payment');
    }
}

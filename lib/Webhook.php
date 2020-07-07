<?php

namespace Beebmx\KirbyPay;

use Illuminate\Support\Str;
use Kirby\Http\Request;

class Webhook
{
    /**
     * Webhooks request
     *
     * @var array
     */

    protected $payload = [];

    /**
     * Payment instance if found
     *
     * @var
     */
    protected $payment;

    /**
     * Webhook constructor.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->payload = $request->get();
    }

    /**
     * Handler for all the webhook's request
     *
     * @return array
     */
    public function handle()
    {
        $payment = null;
        if (method_exists($this, $method = $this->eventToMethod($this->payload['type']))) {
            $payment = $this->$method();
        }

        kirby()->trigger('beebmx.kirby-pay.' . $this->payload['type'], ['payment' => $payment]);
        return ['message' => 'Webhook Received'];
    }

    /**
     * Handle charge.created webhook event
     *
     * @return Payment|bool
     */
    public function handleChargeCreated()
    {
        return $this->notifyPayment();
    }

    /**
     * Handle charge.captured webhook event
     *
     * @return Payment|bool
     */
    public function handleChargeCaptured()
    {
        return $this->notifyPayment();
    }

    /**
     * Handle charge.paid webhook event
     *
     * @return Payment|bool
     */
    public function handleChargePaid()
    {
        return $this->processPayment();
    }

    /**
     * Handle charge.succeeded webhook event
     *
     * @return Payment|bool
     */
    public function handleChargeSucceeded()
    {
        return $this->processPayment();
    }

    /**
     * Handle charge.expired webhook event
     *
     * @return Payment|bool
     */
    public function handleChargeExpired()
    {
        return $this->processPayment();
    }

    /**
     * Handle charge.failed webhook event
     *
     * @return Payment|bool
     */
    public function handleChargeFailed()
    {
        return $this->processPayment();
    }

    /**
     * Handle charge.updated webhook event
     *
     * @return Payment|bool
     */
    public function handleChargeUpdated()
    {
        return $this->processPayment();
    }

    /**
     * Handle charge.refunded webhook event
     *
     * @return Payment|bool
     */
    public function handleChargeRefunded()
    {
        $payment = $this->getPayment();
        if ($payment) {
            $amount = $payment->amount;
            $refunded = 0;

            if (isset($this->payload['data']['object']['amount']) && isset($this->payload['data']['object']['amount_refunded'])) {
                $amount = abs(Payment::parseAmount($this->payload['data']['object']['amount']));
                $refunded = abs(Payment::parseAmount($this->payload['data']['object']['amount_refunded']));
            } elseif (isset($this->payload['data']['object']['amount']) && isset($this->payload['data']['object']['refunds']['data']['amount'])) {
                $amount = abs(Payment::parseAmount($this->payload['data']['object']['amount']));
                $refunded = abs(Payment::parseAmount($this->payload['data']['object']['refunds']['data']['amount']));
            }

            $payment->amount = (int) $amount - (int) $refunded;
            $payment->status = 'refunded';
            $payment->save();
        }

        $this->saveLog();
        return $payment;
    }

    /**
     * Handle charge.partially_refunded webhook event
     *
     * @return Payment|bool
     */
    public function handleChargePartiallyRefunded()
    {
        $payment = $this->getPayment();

        if ($payment) {
            $amount = $payment->amount;
            $refunded = 0;

            if (isset($this->payload['data']['object']['amount']) && isset($this->payload['data']['object']['refunds']['data']['amount'])) {
                $amount = abs(Payment::parseAmount($this->payload['data']['object']['amount']));
                $refunded = abs(Payment::parseAmount($this->payload['data']['object']['refunds']['data']['amount']));
            }

            $payment->amount = (int) $amount - (int) $refunded;
            $payment->status = 'refunded';
            $payment->save();
        }

        $this->saveLog();
        return $payment;
    }

    /**
     * Handle order.partially_refunded webhook event
     *
     * @return Payment|bool
     */
    public function handleOrderPartiallyRefunded()
    {
        return $this->processPayment();
    }

    /**
     * Handle payment_intent.created webhook event
     *
     * @return Payment|bool
     */
    public function handlePaymentIntentCreated()
    {
        return $this->processPayment();
    }

    /**
     * Handle payment_intent.succeeded webhook event
     *
     * @return Payment|bool
     */
    public function handlePaymentIntentSucceeded()
    {
        return $this->processPayment();
    }

    /**
     * Handle order.paid webhook event
     *
     * @return Payment|bool
     */
    public function handleOrderPaid()
    {
        return $this->processPayment();
    }

    /**
     * Handle charge.chargeback.created webhook event
     *
     * @return Payment|bool
     */
    public function handleChargeChargebackCreated()
    {
        $payment = $this->updatePaymentStatus();
        $this->saveLog([
            'id' => $payment->id ?? null
        ]);

        return $payment;
    }

    /**
     * Handle test.webhook webhook event
     *
     * @return Payment
     */
    public function handleTestWebhook()
    {
        $payment = Payment::first();
        $payment->status = 'created';

        $this->saveLog([
            'id' => $payment->id ?? 'test_id',
            'test' => 'webhook',
            'type' => 'test.webhook',
            'status' => 'created',
        ]);

        return $payment;
    }

    /**
     * Save payment in log file
     *
     * @return Payment|bool
     */
    protected function notifyPayment()
    {
        $this->saveLog();
        return $this->getPayment();
    }

    /**
     * Process payment and save log file
     *
     * @return Payment|bool
     */
    protected function processPayment()
    {
        $payment = $this->updatePaymentStatus();
        $this->saveLog([
            'id' => $payment->id ?? null
        ]);

        return $payment;
    }

    /**
     * Update payment status
     *
     * @return Payment|bool
     */
    protected function updatePaymentStatus()
    {
        $payment = $this->getPayment();
        if ($payment) {
            $payment->status = $this->getStatus($payment);
            $payment->save();
        }

        return $payment;
    }

    /**
     * Get payment id from payload
     *
     * @return string|null
     */
    protected function getPaymentId()
    {
        if (isset($this->payload['data']['object']['payment_intent'])) {
            return $this->payload['data']['object']['payment_intent'];
        } elseif (isset($this->payload['data']['payment_intent'])) {
            return $this->payload['data']['payment_intent'];
        } elseif (isset($this->payload['payment_intent'])) {
            return $this->payload['payment_intent'];
        } elseif (isset($this->payload['data']['object']['order_id'])) {
            return $this->payload['data']['object']['order_id'];
        } elseif (isset($this->payload['data']['order_id'])) {
            return $this->payload['data']['order_id'];
        } elseif (isset($this->payload['order_id'])) {
            return $this->payload['order_id'];
        } elseif (isset($this->payload['data']['object']['id'])) {
            return $this->payload['data']['object']['id'];
        } elseif (isset($this->payload['data']['id'])) {
            return $this->payload['data']['id'];
        } elseif (isset($this->payload['id'])) {
            return $this->payload['id'];
        }

        return null;
    }

    /**
     * Get payment instance
     *
     * @return Payment|bool
     */
    protected function getPayment()
    {
        if (!$this->payment) {
            $this->payment = Payment::search($this->getPaymentId(), 'id')->first();
            return $this->payment;
        }

        return $this->payment;
    }

    /**
     * Get payment status from payload
     *
     * @param Payment $payment
     * @return string
     */
    protected function getStatus(Payment $payment)
    {
        $status = $payment->status;

        if (isset($this->payload['data']['object']['status'])) {
            $status = $this->payload['data']['object']['status'];
        } elseif (isset($this->payload['data']['status'])) {
            $status = $this->payload['data']['status'];
        } elseif (isset($this->payload['status'])) {
            $status = $this->payload['status'];
        } elseif (isset($this->payload['data']['object']['payment_status'])) {
            $status = $this->payload['data']['object']['payment_status'];
        } elseif (isset($this->payload['data']['payment_status'])) {
            $status = $this->payload['data']['payment_status'];
        } elseif (isset($this->payload['payment_status'])) {
            $status = $this->payload['payment_status'];
        }

        return $status;
    }

    /**
     * Save attributes to log file
     *
     * @param array $extra
     * @return void
     */
    protected function saveLog(array $extra = [])
    {
        if ((bool) pay('logs', false)) {
            Log::create(array_merge(
                $extra,
                $this->payload
            ));
        }
    }

    /**
     * Get the method depending of the webhook event
     *
     * @param $event
     * @return string
     */
    protected function eventToMethod($event)
    {
        return 'handle' . Str::studly(str_replace('.', '_', $event));
    }
}

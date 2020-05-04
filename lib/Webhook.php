<?php

namespace Beebmx\KirbyPay;

use Illuminate\Support\Str;
use Kirby\Http\Request;

class Webhook
{
    protected $request;

    protected $payload;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->payload = $request->get();
    }

    public function handle()
    {
        $payment = null;
        if (method_exists($this, $method = $this->eventToMethod($this->payload['type']))) {
            $payment = $this->$method();
        }

        kirby()->trigger('kirby-pay.' . $this->payload['type'], $payment);
        return ['message' => 'Webhook Received'];
    }

    public function handleChargePaid()
    {
        $id = $this->getPaymentId();
        $payment = $this->updatePayment($id);

        $this->saveLog(['order_id' => $id]);
        return $payment;
    }

    public function handleOrderPaid()
    {
        $id = $this->getPaymentId();
        $payment = $this->updatePayment($id);

        $this->saveLog(['order_id' => $id]);
        return $payment;
    }
    
    public function handleTestWebhook()
    {
        $payment = new Payment;
        $payment->status = 'created';

        $this->saveLog([
            'test' => 'webhook',
            'type' => 'test.webhook',
            'status' => 'created',
        ]);

        return $payment;
    }

    protected function updatePayment($id)
    {
        $payment = Payment::search($id, 'order_id')->first();
        if ($payment) {
            $payment->status = $this->getStatus($payment);
            $payment->save();
        }

        return $payment;
    }

    protected function getPaymentId()
    {
        if (isset($this->payload['data']['object']['order_id'])) {
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

    protected function saveLog(array $extra = [])
    {
        if ((bool) pay('logs', false)) {
            Log::create(array_merge(
                $extra,
                $this->payload
            ));
        }
    }

    protected  function eventToMethod($event)
    {
        return 'handle' . Str::studly(str_replace('.', '_', $event));
    }
}

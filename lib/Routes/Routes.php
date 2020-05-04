<?php

namespace Beebmx\KirbyPay\Routes;

use Beebmx\KirbyPay\Concerns\ManagesRoutes;
use Beebmx\KirbyPay\Concerns\ValidateRoutes;
use Beebmx\KirbyPay\Contracts\Routable;
use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Webhook;
use Exception;
use Illuminate\Support\Collection;
use Kirby\Http\Request;

class Routes implements Routable
{
    use ManagesRoutes, ValidateRoutes;

    public function all(): array
    {
        return [
            $this->createPaymentCard(),
            $this->handleWebhooks(),
        ];
    }

    public function createPaymentCard()
    {
        $self = $this;
        return [
            'pattern' => static::getBaseApiPath() . 'payment/create',
            'name' => 'payment.create',
            'method' => 'POST',
            'action' => function () use ($self) {
                $request = new Request();

                if(csrf($request->csrf()) !== true) {
                    return [
                        'success' => false,
                        'error' => true,
                        'errors' => kpT('validation.token'),
                        'request' => $request->csrf()
                    ];
                }

                $process = pay('payment_process', 'charge');
                $inputs = $self->getInputs(['name', 'email', 'phone', 'token', 'type', 'process', 'items', 'address', 'state', 'city', 'postal_code', 'country']);

                $customer = $self->only($inputs, ['name', 'email', 'phone']);
                $items = new Collection($self->get($inputs, 'items'));
                $token = $self->get($inputs, 'token');
                $type = $self->get($inputs, 'type');

                $customerError = $self->validateCustomer($customer);
                $shippingError = [];

                if ((bool) pay('shipping', false)) {
                    $shipping = $self->only($inputs, ['address', 'state', 'city', 'postal_code', 'country']);
                    $shippingError = $self->validateShipping($shipping);
                }

                if ($customerError || $shippingError) {
                    return $self->hasErrors($customerError, $shippingError);
                } else {
                    try {
                        $payment = Payment::$process(
                            $customer,
                            $items,
                            $token,
                            $type,
                            $shipping ?? null,
                        );
                        return [
                            'redirect' => url(pay('redirect', 'thanks'), ['params' => ['id' => $payment->uuid]]),
                            'success' => true,
                            'error' => false,
                        ];
                    } catch (Exception $e) {
                        return [
                            'success' => false,
                            'error' => true,
                            'errors' => $e->getMessage(),
                        ];
                    }
                }
            },
        ];
    }

    public function handleWebhooks()
    {
        return [
            'pattern' => static::getBaseApiPath() . 'webhook',
            'name' => 'webhook.handle',
            'method' => 'POST',
            'action' => function () {
                $request = new Request;
                return (new Webhook($request))->handle();
            }
        ];
    }
}

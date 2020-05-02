<?php

namespace Beebmx\KirbyPay\Routes;

use Beebmx\KirbyPay\Concerns\ManagesRoutes;
use Beebmx\KirbyPay\Concerns\ValidateRoutes;
use Beebmx\KirbyPay\Contracts\Routable;
use Beebmx\KirbyPay\Payment;
use Exception;
use Illuminate\Support\Collection;

class Routes implements Routable
{
    use ManagesRoutes, ValidateRoutes;

    public function all(): array
    {
        return [
            $this->createPaymentCard(),
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
                            'redirect' => url(pay('redirect', 'thanks'), ['params' => ['id' => $payment['uuid']]]),
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
}

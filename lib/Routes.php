<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Concerns\ManagesRoutes;
use Beebmx\KirbyPay\Contracts\Routable;
use Exception;
use Illuminate\Support\Collection;
use Kirby\Cms\Response;

class Routes implements Routable
{
    use ManagesRoutes;

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
            'pattern' => static::getBaseApiPath() . 'payment/card/create',
            'name' => 'payment.card.create',
            'method' => 'POST',
            'action' => function () use ($self) {
                $process = pay('default_payment_process', 'charge');
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
                        $payment = (new Payment)->{$process}(
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

    public function getInputs(array $inputs)
    {
        return (new Collection(get()))->only($inputs);
    }

    public function only(Collection $inputs, array $values)
    {
        return $inputs->only($values)->filter(function ($value) {
            return !empty($value);
        });
    }

    public function get($inputs, $value)
    {
        return $inputs->only([$value])->get($value);
    }

    public function validateCustomer(Collection $customer)
    {
        return invalid(
            $customer->toArray(),
            [
                'name' => ['required', 'min' => 5],
                'email' => ['required', 'email'],
                'phone' => ['required', 'minLength' => 10],
            ],
            [
                'name' => kpT('validation.name'),
                'email' => kpT('validation.email'),
                'phone' => kpT('validation.phone'),
            ]
        );
    }

    public function validateShipping(Collection $shipping)
    {
        return invalid(
            $shipping->toArray(),
            [
                'address' => ['required', 'min' => 10],
                'state' => ['required', 'min' => 2],
                'country' => ['required', 'minLength' => 2, 'maxLength' => 2],
                'postal_code' => ['required', 'minLength' => 4],
            ],
            [
                'address' => kpT('validation.address'),
                'state' => kpT('validation.state'),
                'country' => kpT('validation.country'),
                'postal_code' => kpT('validation.postal-code'),
            ]
        );
    }

    public function hasErrors(array $customerError, array $shippingError)
    {
        return [
            'success' => false,
            'error' => true,
            'errors' => array_merge($customerError ?? [], $shippingError),
        ];
    }
}

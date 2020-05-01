<?php

namespace Beebmx\KirbyPay\Concerns;

use Illuminate\Support\Collection;

trait ValidateRoutes
{
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
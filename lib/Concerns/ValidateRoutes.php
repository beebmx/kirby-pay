<?php

namespace Beebmx\KirbyPay\Concerns;

use Illuminate\Support\Collection;
use Kirby\Http\Request;

trait ValidateRoutes
{
    public static function getInputs(Request $request, array $inputs)
    {
        return (new Collection($request->get()))->only($inputs);
    }

    public static function only(array $inputs, array $values): Collection
    {
        return (new Collection($inputs))->only($values)->filter(function ($value) {
            return !empty($value);
        });
    }

    public static function get($inputs, $value)
    {
        return $inputs->only([$value])->get($value);
    }

    public static function validateCustomer(Collection $customer)
    {
        return invalid(
            $customer->toArray(),
            [
                'name' => ['required', 'min' => 5],
                'email' => ['required', 'email'],
                'phone' => ['required', 'minLength' => 6],
            ],
            [
                'name' => kpT('validation.name'),
                'email' => kpT('validation.email'),
                'phone' => kpT('validation.phone'),
            ]
        );
    }

    public static function validateItems(Collection $items)
    {
        return $items->map(function ($item) {
            return [
                'item' => invalid(
                    $item,
                    [
                        'name' => ['required'],
                        'amount' => ['required'],
                        'quantity' => ['required'],
                    ],
                    [
                        'name' => kpT('validation.items.name'),
                        'amount' => kpT('validation.items.amount'),
                        'quantity' => kpT('validation.items.quantity'),
                    ]
                )
            ];
        })->map(function ($item){
            return !empty($item['item']) ? $item : null;
        })->first(function ($item) {
            return is_array($item) && !empty($item);
        }) ?? [];
    }

    public static function validateShipping(Collection $shipping)
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

    public static function hasErrors(array ...$errors)
    {
        return [
            'success' => false,
            'error' => true,
            'errors' => array_merge(...$errors),
        ];
    }

    protected static function hasPaymentFields(Request $request)
    {
        if (!static::hasCsrf($request)) {
            return static::setErrorType('csrf-token');
        } elseif (!static::hasField($request, 'token')) {
            return static::setErrorType('token');
        } elseif (!static::hasField($request, 'type')) {
            return static::setErrorType('type');
        } elseif (!static::hasField($request, 'customer')) {
            return static::setErrorType('customer');
        } elseif (!static::hasField($request, 'items')) {
            return static::setErrorType('items');
        } elseif (!static::hasField($request, 'shipping') && kpHasShipping()) {
            return static::setErrorType('shipping');
        }

        return true;
    }

    protected static function hasCustomerFields(Request $request)
    {
        if (!static::hasCsrf($request)) {
            return static::setErrorType('csrf-token');
        } elseif (!static::hasField($request, 'token')) {
            return static::setErrorType('token');
        } elseif (!static::hasField($request, 'customer')) {
            return static::setErrorType('customer');
        }

        return true;
    }

    protected static function hasCsrf(Request $request): bool
    {
        return csrf($request->csrf()) === true;
    }

    protected static function hasField(Request $request, string $type): bool
    {
        return !empty($request->get($type));
    }

    protected static function setErrorType(string $type): array
    {
        return [
            'success' => false,
            'error' => true,
            'errors' => kpT("validation.{$type}"),
            'type' => $type
        ];
    }
}

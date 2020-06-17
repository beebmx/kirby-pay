<?php

namespace Beebmx\KirbyPay\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kirby\Http\Request;

trait ValidateRoutes
{
    /**
     * Get inputs through request
     *
     * @param Request $request
     * @param array $inputs
     * @return Collection
     */
    public static function getInputs(Request $request, array $inputs)
    {
        return (new Collection($request->get()))->only($inputs);
    }

    /**
     * Get only selected input in array
     *
     * @param array $inputs
     * @param array $values
     * @return Collection
     */
    public static function only(array $inputs, array $values): Collection
    {
        return (new Collection($inputs))->only($values)->filter(function ($value) {
            return !empty($value);
        });
    }

    /**
     * Get only one value from input collection
     *
     * @param $inputs
     * @param $value
     * @return mixed
     */
    public static function get($inputs, $value)
    {
        return $inputs->only([$value])->get($value);
    }

    /**
     * Validate customer inputs from collection
     *
     * @param Collection $customer
     * @return array|false
     */
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

    /**
     * Validate items from collection
     *
     * @param Collection $items
     * @return array|mixed
     */
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
        })->map(function ($item) {
            return !empty($item['item']) ? $item : null;
        })->first(function ($item) {
            return is_array($item) && !empty($item);
        }) ?? [];
    }

    /**
     * Validate extra_amounts from collection
     *
     * @param Collection $extra_amounts
     * @return array|mixed
     */
    public static function validateExtraAmounts(Collection $extra_amounts)
    {
        return $extra_amounts->map(function ($amount, $key) {
            return [
                $key => invalid(
                    [$key => $amount],
                    [
                        $key => ['required', 'num'],
                    ],
                    [
                        $key => kpT('validation.extra_amounts'),
                    ]
                )
            ];
        })->map(function ($item, $key) {
            return !empty($item[$key]) ? $item : null;
        })->first(function ($item) {
            return is_array($item) && !empty($item);
        }) ?? [];
    }

    /**
     * Validate shipping from collection
     *
     * @param Collection $shipping
     * @return array|false
     */
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

    /**
     * Set arrays errors
     * @param array ...$errors
     * @return array
     */
    public static function hasErrors(array ...$errors)
    {
        return [
            'success' => false,
            'error' => true,
            'errors' => array_merge(...$errors),
        ];
    }

    /**
     * Validate request payment fields
     *
     * @param Request $request
     * @return array|bool
     */
    protected static function hasPaymentFields(Request $request)
    {
        if (!static::hasCsrf($request)) {
            return static::setErrorType('csrf-token');
        } elseif (!static::hasField($request, 'token', true)) {
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

    /**
     * Validate request order fields
     *
     * @param Request $request
     * @return array|bool
     */
    protected static function hasOrderFields(Request $request)
    {
        if (!static::hasCsrf($request)) {
            return static::setErrorType('csrf-token');
        } elseif (!static::hasUuid($request, 'id')) {
            return static::setErrorType('id');
        } elseif (!static::hasField($request, 'items')) {
            return static::setErrorType('items');
        } elseif (!static::hasField($request, 'shipping') && kpHasShipping()) {
            return static::setErrorType('shipping');
        }

        return true;
    }

    /**
     * Validate request customer fields
     *
     * @param Request $request
     * @return array|bool
     */
    protected static function hasCustomerFields(Request $request)
    {
        if (!static::hasCsrf($request)) {
            return static::setErrorType('csrf-token');
        } elseif (!static::hasField($request, 'token', true)) {
            return static::setErrorType('token');
        } elseif (!static::hasField($request, 'customer')) {
            return static::setErrorType('customer');
        }

        return true;
    }

    /**
     * Validate request customer update fields
     *
     * @param Request $request
     * @return array|bool
     */
    protected static function hasCustomerUpdateFields(Request $request)
    {
        if (!static::hasCsrf($request)) {
            return static::setErrorType('csrf-token');
        } elseif (!static::hasUuid($request, 'id')) {
            return static::setErrorType('id');
        } elseif (!static::hasField($request, 'customer')) {
            return static::setErrorType('customer');
        }

        return true;
    }

    /**
     * Validate request source update fields
     *
     * @param Request $request
     * @return array|bool
     */
    protected static function hasSourceUpdateFields(Request $request)
    {
        if (!static::hasCsrf($request)) {
            return static::setErrorType('csrf-token');
        } elseif (!static::hasUuid($request, 'id')) {
            return static::setErrorType('id');
        } elseif (!static::hasField($request, 'token', true)) {
            return static::setErrorType('token');
        }

        return true;
    }

    /**
     * Determine if has csrf token in request
     *
     * @param Request $request
     * @return bool
     */
    protected static function hasCsrf(Request $request): bool
    {
        return csrf($request->csrf()) === true;
    }

    /**
     * Determine if request has valid UUID
     *
     * @param Request $request
     * @param $field
     * @return bool
     */
    protected static function hasUuid(Request $request, $field): bool
    {
        return Str::isUuid((string) $request->get($field));
    }

    /**
     * Determine if a field exist in request
     *
     * @param Request $request
     * @param string $field
     * @param bool $empty
     * @return bool
     */
    protected static function hasField(Request $request, string $field, bool $empty = false): bool
    {
        if ($empty) {
            return array_key_exists($field, $request->get());
        }

        return array_key_exists($field, $request->get()) && !empty($request->get($field));
    }

    /**
     * Set error type if some validation fails
     *
     * @param string $field
     * @return array
     */
    protected static function setErrorType(string $field): array
    {
        return [
            'success' => false,
            'error' => true,
            'errors' => kpT("validation.{$field}"),
            'type' => $field
        ];
    }
}

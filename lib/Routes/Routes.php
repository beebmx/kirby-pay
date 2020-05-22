<?php

namespace Beebmx\KirbyPay\Routes;

use Beebmx\KirbyPay\Concerns\ManagesRoutes;
use Beebmx\KirbyPay\Concerns\ValidateRoutes;
use Beebmx\KirbyPay\Contracts\Routable;
use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Webhook;
use Exception;
use Illuminate\Support\Collection;
use Kirby\Http\Request;

class Routes implements Routable
{
    use ManagesRoutes, ValidateRoutes;

    public static function all(): array
    {
        return [
            static::createPayment(),
            static::createCustomer(),
            static::handleWebhooks(),
        ];
    }

    public static function createPayment()
    {
        return [
            'pattern' => static::getBaseApiPath() . 'payment/create',
            'name' => 'payment.create',
            'method' => 'POST',
            'action' => function () {
                return Routes::handleCreatePayment(new Request);
            },
        ];
    }

    public static function createCustomer()
    {
        return [
            'pattern' => static::getBaseApiPath() . 'customer/create',
            'name' => 'customer.create',
            'method' => 'POST',
            'action' => function () {
                return Routes::handleCreateCustomer(new Request);
            },
        ];
    }

    public static function handleWebhooks()
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

    public static function handleCreatePayment(Request $request)
    {
        if (($requiredError = static::hasPaymentFields($request)) !== true) {
            return $requiredError;
        }

        $process = pay('payment_process', 'charge');
        $inputs = static::getInputs($request, ['token', 'type', 'customer', 'items', 'shipping']);

        $customer = static::only($inputs->get('customer'), ['name', 'email', 'phone']);
        $items = new Collection($inputs->get('items'));
        $token = $inputs->get('token');
        $type = $inputs->get('type');

        $customerError = static::validateCustomer($customer);
        $itemsError = static::validateItems($items);
        $shippingError = [];

        if (kpHasShipping()) {
            $shipping = static::only($inputs->get('shipping'), ['address', 'state', 'city', 'postal_code', 'country']);
            $shippingError = static::validateShipping($shipping);
        }

        if ($customerError || $itemsError || $shippingError) {
            return static::hasErrors($customerError, $itemsError, $shippingError);
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
    }

    public static function handleCreateCustomer(Request $request)
    {
        if (($requiredError = static::hasCustomerFields($request)) !== true) {
            return $requiredError;
        }

        $inputs = static::getInputs($request, ['token', 'customer']);
        $customer = static::only($inputs->get('customer'), ['name', 'email', 'phone']);
        $token = $inputs->get('token');

        $customerError = static::validateCustomer($customer);

        if ($customerError) {
            return static::hasErrors($customerError);
        } else {
            try {

                $resource = Customer::create(
                    new Buyer(
                        $customer['name'],
                        $customer['email'],
                        $customer['phone'],
                    ),
                    $token,
                    'card',
                );
                return [
                    'redirect' => url(pay('redirect-customer-create', 'customer-created'), ['params' => ['id' => $resource->uuid]]),
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
    }
}

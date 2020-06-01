<?php

namespace Beebmx\KirbyPay\Routes;

use Beebmx\KirbyPay\Concerns\ManagesRoutes;
use Beebmx\KirbyPay\Contracts\Routable;
use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Log;
use Beebmx\KirbyPay\Payment;

class ApiRoutes implements Routable
{
    use ManagesRoutes;

    /**
     * Get all the api routes available
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            static::config(),
            static::getPayments(),
            static::getPayment(),
            static::getCustomers(),
            static::getCustomer(),
            static::getDevelopment(),
        ];
    }

    /**
     * Get the Kirby Pay configuration panel
     *
     * @return array
     */
    public static function config()
    {
        return [
            'pattern' => 'beebmx/kirby-pay/config',
            'method' => 'GET',
            'action' => function () {
                return [
                    'success' => true,
                    'service' => [
                        'name' => ucfirst(pay('service', 'sandbox')),
                        'customers' => Customer::serviceUrl(),
                        'payments' => Payment::serviceUrl(),
                        'logs' => Log::serviceUrl(),
                    ],
                    'resources' => [
                        'payments' => !Payment::isEmpty(),
                        'customers' => !Customer::isEmpty(),
                    ],
                    'development' => kpInDevelopment(),
                ];
            }
        ];
    }

    /**
     * Get the payment resources for panel
     *
     * @return array
     */
    public static function getPayments()
    {
        return [
            'pattern' => 'beebmx/kirby-pay/payments/(:num)',
            'method' => 'GET',
            'action' => function (int $page) {
                return [
                    'success' => true,
                    'payments' => Payment::page($page, pay('pagination', 10))->diffForHumans()->get(),
                    'resource' => [
                        'total' => Payment::count(),
                        'page' => $page,
                        'pagination' => pay('pagination', 10),
                    ],
                ];
            }
        ];
    }

    /**
     * Get a payment resoruce for panel
     *
     * @return array
     */
    public static function getPayment()
    {
        return [
            'pattern' => 'beebmx/kirby-pay/payment/(:any)',
            'method' => 'GET',
            'action' => function ($id) {
                $payment = Payment::find((string) $id);
                $next = Payment::find((int) $payment->pay_id + 1);
                $prev = Payment::find((int) $payment->pay_id - 1);
                return [
                    'success' => true,
                    'id' => (int) $id,
                    'payment' => $payment,
                    'next' => $next->uuid ?? false,
                    'prev' => $prev->uuid ?? false,
                ];
            }
        ];
    }

    /**
     * Get customer resources for panel
     *
     * @return array
     */
    public static function getCustomers()
    {
        return [
            'pattern' => 'beebmx/kirby-pay/customers/(:num)',
            'method' => 'GET',
            'action' => function (int $page) {
                return [
                    'success' => true,
                    'customers' => Customer::page($page, pay('pagination', 10))->diffForHumans()->get(),
                    'resource' => [
                        'total' => Customer::count(),
                        'page' => $page,
                        'pagination' => pay('pagination', 10),
                    ],
                ];
            }
        ];
    }

    /**
     * Get customer resource for panel
     *
     * @return array
     */
    public static function getCustomer()
    {
        return [
            'pattern' => 'beebmx/kirby-pay/customer/(:any)',
            'method' => 'GET',
            'action' => function ($id) {
                $customer = Customer::find((string) $id);
                $next = Customer::find((int) $customer->pay_id + 1);
                $prev = Customer::find((int) $customer->pay_id - 1);
                return [
                    'success' => true,
                    'id' => (int) $id,
                    'customer' => $customer,
                    'next' => $next['uuid'] ?? false,
                    'prev' => $prev['uuid'] ?? false,
                ];
            }
        ];
    }

    /**
     * Get development data for panel
     *
     * @return array
     */
    public static function getDevelopment()
    {
        return [
            'pattern' => 'beebmx/kirby-pay/development/(:num)',
            'method' => 'GET',
            'action' => function (int $page) {
                return [
                    'success' => true,
                    'webhook' => url(ApiRoutes::getBaseApiPath() . 'webhook'),
                    'logs' => Log::page($page, pay('pagination', 10))->diffForHumans()->get(),
                    'resource' => [
                        'total' => Log::count(),
                        'page' => $page,
                        'pagination' => pay('pagination', 10),
                    ],
                ];
            }
        ];
    }
}

<?php

namespace Beebmx\KirbyPay\Routes;

use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Payment;

class ApiRoutes
{
    protected $instance;

    public function __construct()
    {
        $this->instance = $this;
    }

    public function all(): array
    {
        return [
            $this->config(),
            $this->getPayments(),
            $this->getPayment(),
            $this->getCustomers(),
            $this->getCustomer(),
        ];
    }

    public function config()
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
                    ],
                    'resources' => [
                        'payments' => !Payment::isEmpty(),
                        'customers' => !Customer::isEmpty(),
                    ],
                ];
            }
        ];
    }

    public function getPayments()
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
                    'service' => ucfirst(pay('service', 'sandbox')),
                    'exists' => [
                        'customers' => !Customer::isEmpty()
                    ],
                ];
            }
        ];
    }

    public function getPayment()
    {
        return [
            'pattern' => 'beebmx/kirby-pay/payment/(:any)',
            'method' => 'GET',
            'action' => function ($id) {
                $payment = Payment::find((string) $id);
                $next = Payment::find((int) $payment->id + 1);
                $prev = Payment::find((int) $payment->id - 1);
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

    public function getCustomers()
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
                    'service' => ucfirst(pay('service', 'sandbox')),
                    'exists' => [
                        'payments' => !Payment::isEmpty()
                    ],
                ];
            }
        ];
    }

    public function getCustomer()
    {
        return [
            'pattern' => 'beebmx/kirby-pay/customer/(:any)',
            'method' => 'GET',
            'action' => function ($id) {
                $customer = Customer::find((string) $id);
                $next = Customer::find((int) $customer->id + 1);
                $prev = Customer::find((int) $customer->id - 1);
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
}

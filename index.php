<?php

use Beebmx\KirbyPay\Routes\ApiRoutes;
use Beebmx\KirbyPay\Routes\Routes;

Kirby::plugin('beebmx/kirby-pay', [
    'options' => [
        'name' => 'Business',
        'env' => 'production',
        'service' => 'sandbox',
        'service_key' => null,
        'service_secret' => null,
        'locale' => 'en_US',
        'locale_code' => 'en',
        'currency' => 'usd',
        'money_precision' => 2,
        'date_format' => 'Y-m-d H:m:s',
        'shipping' => false,
        'default_item_name' => 'Item to sell',
        'default_country' => null,
        'payment_types' => ['card'],
        'payment_process' => 'charge',
        'payment_expiration_days' => 30,
        'pay_id_length' => 6,
        'pagination' => 10,
        'redirect' => 'thanks',
        'redirect_customer_create' => 'customer',
        'redirect_customer_update' => 'profile',
        'redirect_source_update' => 'profile',
        'extra_amounts_item' => 'Extra',
        'countries' => null,
        'logs' => false,
        'storage' => function () {
            return kirby()->roots()->index() . '/pay';
        },
        'styles' => [
            'form' => 'kp-form',
            'fieldset' => 'kp-fieldset',
            'legend' => 'kp-legend',
            'background' => 'kp-bg-white',
            'text' => 'kp-text-current',
            'text-inverse' => 'kp-text-white',
            'field' => 'kp-field',
            'label' => 'kp-label',
            'input' => 'kp-input',
            'select' => 'kp-select',
            'button' => 'kp-button',
            'button-disabled' => 'kp-button-disabled',
            'error' => 'kp-text-red',
            'errors' => 'kp-errors',
            'alert' => 'kp-alert',
            'payment-selector' => 'kp-payment-selector',
            'methods' => 'kp-payment-methods',
            'radio' => 'kp-radio',
            'radio-label' => 'kp-radio-label',
            'radio-header' => 'kp-radio-label-header',
            'radio-body' => 'kp-radio-label-body',
            'title' => 'kp-title',
            'col' => 'kp-method-column',
            'alert-icon' => '<svg class="kp-w-5 kp-h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>',
            'stripe-input' => [],
            'stripe-input-default' => [
                'color' => '#1A202C',
                'fontSize' => '16px',
                'fontSmoothing' => 'antialiased',
                '::placeholder' => [
                    'color' => '#CBD5E0',
                ]
            ],
            'stripe-invalid' => [],
            'stripe-invalid-default' => [
                'color' => '#E53E3E',
            ]
        ],
    ],
    'routes' => function ($kirby) {
        return (new Routes())->all();
    },
    'api' => [
        'routes' => function ($kirby) {
            return (new ApiRoutes)->all();
        }
    ],
    'translations' => [
        'en' => require(__DIR__ . '/languages/en.php'),
        'es' => require(__DIR__ . '/languages/es.php'),
        'es_419' => require(__DIR__ . '/languages/es.php'),
        'es_ES' => require(__DIR__ . '/languages/es.php'),
    ],
    'snippets' => [
        'kirby-pay.styles' => __DIR__ . '/snippets/styles.php',
        'kirby-pay.payment' => __DIR__ . '/snippets/payment.php',
        'kirby-pay.payment.sandbox' => __DIR__ . '/snippets/payment/sandbox.php',
        'kirby-pay.payment.conekta' => __DIR__ . '/snippets/payment/conekta.php',
        'kirby-pay.payment.stripe' => __DIR__ . '/snippets/payment/stripe.php',
        'kirby-pay.order' => __DIR__ . '/snippets/order.php',
        'kirby-pay.customer' => __DIR__ . '/snippets/customer.php',
        'kirby-pay.customer.sandbox' => __DIR__ . '/snippets/customer/sandbox.php',
        'kirby-pay.customer.conekta' => __DIR__ . '/snippets/customer/conekta.php',
        'kirby-pay.customer.stripe' => __DIR__ . '/snippets/customer/stripe.php',
        'kirby-pay.customer.update' => __DIR__ . '/snippets/customer/update.php',
        'kirby-pay.customer.source' => __DIR__ . '/snippets/source.php',
        'kirby-pay.customer.source.sandbox' => __DIR__ . '/snippets/customer/sandbox-source.php',
        'kirby-pay.customer.source.conekta' => __DIR__ . '/snippets/customer/conekta-source.php',
        'kirby-pay.customer.source.stripe' => __DIR__ . '/snippets/customer/stripe-source.php',
        'kirby-pay.form.customer' => __DIR__ . '/snippets/form/customer.php',
        'kirby-pay.form.shipping' => __DIR__ . '/snippets/form/shipping.php',
        'kirby-pay.form.payment-methods' => __DIR__ . '/snippets/form/payment-methods.php',
        'kirby-pay.form.errors' => __DIR__ . '/snippets/form/errors.php',
        'kirby-pay.form.button' => __DIR__ . '/snippets/form/button.php',
        'kirby-pay.oxxo-styles' => __DIR__ . '/snippets/oxxo-styles.php',
        'kirby-pay.oxxo' => __DIR__ . '/snippets/oxxo.php',
    ],
]);

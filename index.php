<?php

use Beebmx\KirbyPay\Routes\ApiRoutes;
use Beebmx\KirbyPay\Routes\Routes;

\Illuminate\Support\Collection::macro('recursive', function () {
    return $this->map(function ($value) {
        if (is_array($value) || is_object($value)) {
            return collect($value)->recursive();
        }

        return $value;
    });
});

Kirby::plugin('beebmx/kirby-pay', [
    'options' => [
        'env' => 'test',
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
        'default_payment_type' => 'card',
        'default_payment_process' => 'charge',
        'pagination' => 10,
        'redirect' => 'thanks',
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
    'routes' => (new Routes())->all(),
    'api' => [
        'routes' => (new ApiRoutes)->all()
    ],
    'translations' => [
        'en' => require(__DIR__ . '/languages/en.php'),
        'es' => require(__DIR__ . '/languages/es.php'),
        'es_419' => require(__DIR__ . '/languages/es.php'),
        'es_ES' => require(__DIR__ . '/languages/es.php'),
    ],
    'snippets' => [
        'kirby-pay.styles' => __DIR__ . '/snippets/styles.php',
        'kirby-pay.scripts' => __DIR__ . '/snippets/scripts.php',
        'kirby-pay.form' => __DIR__ . '/snippets/form.php',
        'kirby-pay.form.sandbox' => __DIR__ . '/snippets/forms/sandbox.php',
        'kirby-pay.form.conekta' => __DIR__ . '/snippets/forms/conekta.php',
        'kirby-pay.form.stripe' => __DIR__ . '/snippets/forms/stripe.php',
    ],
]);

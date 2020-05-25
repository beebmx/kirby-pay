# Kirby Pay

*Kirby Pay* offers a painless and easy way to create payments for Kirby, with many things to customize if you want.

## Installation

### Composer

```ssh
composer required beebmx/kirby-pay
```

## Usage

After you install the package just include the snippets and set the items (products or services) to sell:

```php

<header>
...
<?php snippet('kirby-pay.styles') ?>
...
</header>
<body>
...

<!-- Begin Kirby Pay Form -->
<?php
    snippet('kirby-pay.form', [
        'items' => [
            ['name' => 'Product 001', 'amount' => 20.00, 'quantity' => 1],
            ['name' => 'Product 002', 'amount' => 10.00, 'quantity' => 2],
        ],
    ]);
?>
<!-- Ends Kirby Pay Form -->

</body>
```

And that's it, if you use all the default values, it's the only thing you will need.

## General options

This package comes with many things to configure.

| Option | Default | Value | Description |
| ------ | ------- | ----- | ----------- |
| beebmx.kirby-pay.env | production | (string) | Store if the enviroment is `local`, `test` or `production` |
| beebmx.kirby-pay.service' | sandbox | (string) | `sandbox`, `stripe` or `conekta`service |
| beebmx.kirby-pay.service_key' | null | (string) | Set the public key of the service |
| beebmx.kirby-pay.service_secret' | null | (string) | Set the secret key of the service |
| beebmx.kirby-pay.locale | en_US | (string) | Set the locale for the services |
| beebmx.kirby-pay.locale_code | en | (string) | Set the code for Kirby localization |
| beebmx.kirby-pay.currency | usd | (string) | Set the currency with [ISO 4217](https://en.wikipedia.org/wiki/ISO_4217) |
| beebmx.kirby-pay.money_precision | 2 | (int) | Set the precision for the money |
| beebmx.kirby-pay.date_format | Y-m-d H:m:s | (string) | Set the date format for the panel display |
| beebmx.kirby-pay.default_item_name | Item to sell |(string) | Set the default item name for the services |
| beebmx.kirby-pay.shipping | false | (bool) | Hide or show shopping options in the form |
| beebmx.kirby-pay.default_country | null | (string) | If shipping is enable set the default country selected |
| beebmx.kirby-pay.payment_types | ['card'] | (array) | Set the allowed method payments |
| beebmx.kirby-pay.payment_process | charge |(string) | Set the process of the payment `charge` or `order` |
| beebmx.kirby-pay.payment_expiration_days | 30 | (int) | Set the expiration days for `oxxo_charge` in `conekta` service |
| beebmx.kirby-pay.pagination | 10 | (int) | Set the pagination for the panel |
| beebmx.kirby-pay.redirect | thanks |(string) | Set the URL to redirect if the payment was successful |
| beebmx.kirby-pay.storage | /pay | (string) | Set where payment files will be stored |
| beebmx.kirby-pay.logs | false | (bool) | Enable or disable webhooks logs |
| beebmx.kirby-pay.styles |  | (array) | Update default [styles](#styles) |

## Styles

This package comes with pre-build forms for the diferent type of services.
By default, all form are trying to mimic your colors and typography styles and all in the form are just suggest, and you can customize with your own styles or with any framework.

In your `config.php` file just:

```php

return [
    'beebmx.kirby-pay.styles' => [
        'button' => 'button-primary button-block button-small',
        'background' => 'background-white',
    ]
];

```  

Where are the style options than you can update:

| Option | Default |
| ------ | ------- |
| form | kp-form |
| fieldset | kp-fieldset |
| legend | kp-legend |
| background | kp-bg-white |
| text | kp-text-current |
| text-inverse | kp-text-white |
| field | kp-field |
| label | kp-label |
| input | kp-input |
| select | kp-select |
| button | kp-button |
| button-disabled | kp-button-disabled |
| error | kp-text-red |
| errors | kp-errors |
| alert | kp-alert |
| payment-selector | kp-payment-selector |
| methods | kp-payment-methods |
| radio | kp-radio |
| radio-label | kp-radio-label |
| radio-header | kp-radio-label-header |
| radio-body | kp-radio-label-body |
| title | kp-title |
| col | kp-method-column |
| alert-icon | \<svg fill="currentColor" viewBox="0 0 20 20">...\</svg> |
| stripe-input |  |
| stripe-invalid |  | 

### Style examples

If you use some frameworks you can do something like this:

[Tailwind](https://tailwindcss.com)

```php
return [
    'beebmx.kirby-pay.styles' => [
        'button' => 'bg-blue-500 text-white rounded-sm appearance-none px-3 py-2 w-full',
        'background' => 'bg-white',
    ]
];
```

[Bootstrap](https://getbootstrap.com)

```php
return [
    'beebmx.kirby-pay.styles' => [
        'button' => 'btn btn-primary btn-block',
        'background' => 'bg-white',
    ]
];
```

[Bulma](https://bulma.io)

```php
return [
    'beebmx.kirby-pay.styles' => [
        'button' => 'button is-primary is-fullwidth',
        'background' => 'has-background-white',
    ]
];
```

## Webhooks

*Kirby Pay* has many webhooks to hear for every service, for connect it, you need to know which URL you need to set, and for that reason is the `env` option.
Set the `env` to `test` or `local` to enable the development option in the panel:

````php
return [
    'beebmx.kirby-pay.env' => 'local'
];
````

Now you can enter in the development mode and view the Webhook URL.

## Hooks

In the most of the cases you need to notify to the user if the payment was sucessfuly done, and for that reason are hooks.
All the time than you require to implement any hook, you will receive a payment object.
Here is an example to use it with `stripe`:

```php
return [
    'hooks' => [
        'beebmx.kirby-pay.payment_intent.succeeded' => function ($payment) {
            //Send your email
        },
    ]
];
```

As you can see, it's like any webhook of the service but with the prefix of the package `beebmx.kirby-pay.` is the way to catch all the hooks.
You can check the list of the webhooks of the services:

- [Stripe](https://stripe.com/docs/api/events/types)
- [Conekta](https://developers.conekta.com/api#events)

## Development

When you are in developing your shopping cart or any implementation, sometimes you don't need to create a real payment process, for that reason is the `sandbox` service, to simulate any payment but everithing is local.

```php
return [
    'beebmx.kirby-pay.service' => 'sandbox'
];
```

In the `sandbox` service you can try one webhook. Just sent a `POST` request to your webhook URL and send:

```json
{
  "type": "test.webhook"
}
```

You will receive a response like:

```json
{
    "message": "Webhook Received"
}
```

And a hook will be trigger too:

```php
return [
    'hooks' => [
        'beebmx.kirby-pay.test.webhook' => function ($payment) {
            //Test your code
        },
    ],
];
```

### Development logs

When you are working with webhooks, sometimes you need to check the response of the webhook or maybe you just want to store all the events in your server.
To enable that option just:

```php
return [
    'beebmx.kirby-pay.logs' => true,
];
```

## License

Copyright © 2019-2020 Fernando Gutierrez [@beebmx](https://github.com/beebmx) and contributors

Licensed under the MIT license, see [LICENSE.md](LICENSE.md) for details.

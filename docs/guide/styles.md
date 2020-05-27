# Styles
This package comes with pre-build forms for the diferent type of services.
By default, all form are trying to mimic your colors and typography styles and all in the form are just suggest, and you can customize with your own styles or with any framework.

In your `config.php` file add:

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

## Examples

If you use a framework you can do something like this:

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
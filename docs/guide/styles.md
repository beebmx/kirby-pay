# Styles

[[toc]]

## Style list

This package comes with pre-build forms for diferent type of [snippets](snippets).
By default, all forms are trying to mimic your colors and typography styles. All forms are just suggest, and you can customize them with your own styles or with any framework.  

These are the style options than you can customize:

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

### Update styles

To update the default styles, just add the options you need in your `config.php` file:

```php
return [
    'beebmx.kirby-pay.styles' => [
        'button' => 'button-primary button-block button-small',
        'background' => 'background-white',
    ]s
];
```

## Examples

**Kirby Pay** doesn't depend on any framework since it comes with their own styles, but if you need to integrate with your own styles or framework, here's some examples on how you can do implement that:

### Tailwind

```php
return [
    'beebmx.kirby-pay.styles' => [
        'button' => 'bg-blue-500 text-white rounded-sm appearance-none px-3 py-2 w-full',
        'background' => 'bg-white',
    ]
];
```

**Also see:**
- [tailwindcss](https://tailwindcss.com)

### Bootstrap

```php
return [
    'beebmx.kirby-pay.styles' => [
        'button' => 'btn btn-primary btn-block',
        'background' => 'bg-white',
    ]
];
```

**Also see:**
- [Bootstrap](https://getbootstrap.com)

### Bulma

```php
return [
    'beebmx.kirby-pay.styles' => [
        'button' => 'button is-primary is-fullwidth',
        'background' => 'has-background-white',
    ]
];
```

**Also see:**
- [Bulma](https://bulma.io)
# Options

This package comes with many things to configure. All the options comes with the prefix `beebmx.kirby-pay.`.

| Option | Default | Value | Description |
| ------ | ------- | ----- | ----------- |
| env | production | (string) | Store if the enviroment is `local`, `test` or `production` |
| service | sandbox | (string) | `sandbox`, `stripe` or `conekta`service |
| service_key | null | (string) | Set the public key of the service |
| service_secret | null | (string) | Set the secret key of the service |
| locale | en_US | (string) | Set the locale for the services |
| locale_code | en | (string) | Set the code for Kirby localization |
| currency | usd | (string) | Set the currency with [ISO 4217](https://en.wikipedia.org/wiki/ISO_4217) |
| money_precision | 2 | (int) | Set the precision for the money |
| date_format | Y-m-d H<span>:</span>m<span>:</span>s | (string) | Set the date format for the panel display |
| default_item_name | Item to sell |(string) | Set the default item name for the services |
| shipping | false | (bool) | Hide or show shopping options in the form |
| default_country | null | (string) | If shipping is enable set the default country selected |
| payment_types | ['card'] | (array) | Set the allowed method payments |
| payment_process | charge |(string) | Set the process of the payment `charge` or `order` |
| payment_expiration_days | 30 | (int) | Set the expiration days for `oxxo_charge` in `conekta` service |
| pagination | 10 | (int) | Set the pagination for the panel |
| redirect | thanks |(string) | Set the URL to redirect if the payment was successful |
| redirect_customer_create | customer |(string) | Set the URL to redirect if the customer was created |
| redirect_customer_update | profile |(string) | Set the URL to redirect if the customer was updated |
| redirect_source_update | profile |(string) | Set the URL to redirect if the source payment was updated |
| storage | /pay | (string) | Set where payment files will be stored |
| logs | false | (bool) | Enable or disable webhooks logs |
| styles |  | (array) | Update default [styles](/guide/styles) |

## Example

In your `config.php` file add:

```php
$base = dirname(dirname(__DIR__));
$storage = $base . '/storage/pay';

return [
    'beebmx.kirby-pay.service' => 'stripe',
    'beebmx.kirby-pay.service_key' => 'pk_test_stripe_key',
    'beebmx.kirby-pay.service_secret' => 'sk_test_stripe_secret',

    'beebmx.kirby-pay.default_country' => 'US',
    'beebmx.kirby-pay.storage' => $storage,
    'beebmx.kirby-pay.env' => 'test',
    'beebmx.kirby-pay.logs' => true
];
```
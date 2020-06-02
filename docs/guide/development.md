# Development

[[toc]]

## Sandbox

When you are developing a shopping cart or any other payment implementation, sometimes is not necesary to create a real payment flow, for that reason is the `sandbox` driver, to simulate any payment or customer but in a save enviroment without touching any external API.

By default, `sandbox` is set without doing anything, but if you want manually configure this service `driver`, in your Kirby configuration file `config.php` add: 

```php
return [
    'beebmx.kirby-pay.service' => 'sandbox'
];
```

## Webhook

If you are using the `sandbox` driver you can still try a webhook. Just send a `POST` request to the webhook URL with the call of the `webhook`:

```json
{
  "type": "test.webhook"
}
```

You will receive this response:

```json
{
    "message": "Webhook Received"
}
```

::: warning
The `test.webhook` will request for the last payment created. If you don't have any, you will get an error. 
:::


## Hook

If you need to try a `hook`, you can do it with the `test.webhook`. In your Kirby's `config.php` just add:

```php
return [
    'hooks' => [
        'beebmx.kirby-pay.test.webhook' => function ($payment) {
            //Test your code
        },
    ],
];
```

## Logs

When you are working with [webhooks](webhooks), sometimes you need to check the response of the webhook or maybe you just want to store all the events in your server.
To achieve this, you only need to enable the `logs` option in your `config.php` file:

```php
return [
    'beebmx.kirby-pay.logs' => true,
];
```

This logs will be shown in the development section when the configuration is enable.

::: tip
Check the [webhook](webhooks) configuration to enable this option.
:::

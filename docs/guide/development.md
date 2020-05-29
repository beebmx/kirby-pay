# Development

[[toc]]

## Sandbox

When you are developing a shopping cart or any payment implementation, sometimes is not necesary to create a real payment flow, for that reason is the `sandbox` driver, to simulate any payment or customer but everithing in a save enviroment and without touching any external API.

By default, `sandbox` is set without doing anything, but if you want manually configure it this service `driver`, in your `config.php` add: 

```php
return [
    'beebmx.kirby-pay.service' => 'sandbox'
];
```

## Webhook

If you are in the `sandbox` driver you can still try a webhook. Just send a `POST` request to the webhook URL with the call of the `webhook`:

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

If you need to try a `hook`, you can still test it with the `test.webhook`, in `config.php` file just add:

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
To enable that option just add in the `config.php` file:

```php
return [
    'beebmx.kirby-pay.logs' => true,
];
```

This logs will be shown in the development option when the configuration is enable.

::: tip
Check the [webhook](webhooks) configuration to enable this option.
:::

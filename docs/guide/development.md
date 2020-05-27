# Development

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

## Logs

When you are working with webhooks, sometimes you need to check the response of the webhook or maybe you just want to store all the events in your server.
To enable that option just:

```php
return [
    'beebmx.kirby-pay.logs' => true,
];
```

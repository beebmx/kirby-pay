# Hooks

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
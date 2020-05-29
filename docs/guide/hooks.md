# Hooks

[[toc]]

## Configuration

In most cases, you need to notify to the user if the payment was sucessfuly done, and for that reason, there're hooks.
All the time than you require to implement any hook, you will receive a payment resource.

## Hooks available

If you need to listen any [webhook](webhooks), is posible than you need to do something with it.
A `hook` it's like any webhook of the service but with the prefix of the package `beebmx.kirby-pay.`.
You can check the list of the webhooks of the services:

- [Stripe](https://stripe.com/docs/api/events/types)
- [Conekta](https://developers.conekta.com/api#events)

## Example

Here is an example to use it with `stripe` and a webhook of `payment_intent.succeeded`:

```php
return [
    'hooks' => [
        'beebmx.kirby-pay.payment_intent.succeeded' => function ($payment) {
            //Send your email
        },
    ]
];
```

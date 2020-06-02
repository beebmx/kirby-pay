# Hooks

[[toc]]

## Configuration

In most cases, you need to notify the user if the payment was sucessfuly done, and for that reason, there're hooks.
Every time you implement a hook, you will receive a payment resource.

## Hooks available

If you need to listen any [webhook](webhooks), you may need to do something with it.
A `hook` it's named like any event of the service but with the prefix of the package `beebmx.kirby-pay.`.
You can check the list of the events provided by each service in their own API reference.

- [Stripe](https://stripe.com/docs/api/events/types)
- [Conekta](https://developers.conekta.com/api#events)

## Example

Here is an example of use with `stripe` and a webhook for the `payment_intent.succeeded` event:

```php
return [
    'hooks' => [
        'beebmx.kirby-pay.payment_intent.succeeded' => function ($payment) {
            //Send your email
        },
    ]
];
```

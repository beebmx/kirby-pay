# General

## Principles

The idea behind the API of **Kirby Pay** is to give you the tools to create your own flow of payments or improve the experience of the end-user in the payment process.

The main goal is to create a painless experience for the developer in the integration of services like [Stripe](http://stripe.com/) or [Conekta](http://conekta.com/).

For the developer it's just a new instance of an object, but behind the scenes, there is a connection to the chosen service to process a [Customer](customer) or a [Payment](payment).

## Snippet improve

Snippets try to create an easy way to implement the payment process, but sometimes they are not what you need.

The best example of this is to delete or [remove](customer.md#remove) a customer, because there is no [snippet](../guide/snippets) for that action.

But you can create this functionallity through the API with:

```php
use Beebmx\KirbyPay\Customer;

$customer = Customer::find('ae9c2dda-9102-4202-ae3b-1c62da2e03bc');
$customer->remove();
```

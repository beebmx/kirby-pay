# Payment

## Definition

## Methods

### order
- params: ([Buyer](elements.md#buyer) `$customer`, [Items](elements.md#items) `$items`, string `$token`, string `$type` = 'card', [Shipping](elements.md#shipping) `$shipping` = *null*) 
- returns: `Payment`

This `static` method creates a new `Payment` and [Customer](customer) resource.

```php
use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;

Payment::order(
  new Buyer(
    'John Doe',
    'example@email.com',
    '1122334455',
  ),
  new Items([
    new Item('Producto 01', 300.00, 1)
  ]),
  'token'
);
```

### orderWithCustomer
- params: ([Customer](customer) `$customer`, [Items](elements.md#items) `$items`, string `$type` = 'card', [Shipping](elements.md#shipping) `$shipping` = *null*)
- returns: `Payment`

This `static` method creates a new `Payment` resource from a [Customer](customer) instance.

```php
use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;

$customer = Customer::find('4f139ef0-484a-4749-a51f-5ffe339dd1c1');

Payment::orderWithCustomer(
  $customer,
  new Items([
    new Item('Producto 01', 300.00, 1)
  ])
);

```

### Charge
- params: ([Buyer](elements.md#buyer) `$customer`, [Items](elements.md#items) `$items`, string `$token`, string `$type` = 'card', [Shipping](elements.md#shipping) `$shipping` = *null*)
- returns: `Payment`

This `static` method creates a new `Payment` resource.

```php
use Beebmx\KirbyPay\Payment;
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;

Payment::charge(
  new Buyer(
    'John Doe',
    'example@email.com',
    '1122334455',
  ),
  new Items([
    new Item('Producto 01', 300.00, 1)
  ]),
  'token'
);
```

### serviceUrl
- params: `none`
- returns: `string`

This `static` method returns the URL of the payments in the `driver` service.

```php
use Beebmx\KirbyPay\Payment;

Payment::serviceUrl();
```

### getPaymentMethods
- params: `none`
- returns: `array`

This `static` method returns an array with all the available payment methods in the `driver` service.

```php
use Beebmx\KirbyPay\Payment;

Payment::serviceUrl();
```

### parseAmount
- params: (int `$amount`)
- returns: `float`

This `static` method returns the `float` amount depending of the `driver` service.

```php
use Beebmx\KirbyPay\Payment;

Payment::parseAmount(100);
```

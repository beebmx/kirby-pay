# Elements

## Buyer

### __constructor
- params: (string `$name`, string `$email`, string `$phone` = *null*, `$id` = *null*, `$customer_id`)

Creates an instance of `Buyer` element class.

```php
use Beebmx\KirbyPay\Elements\Buyer;

$buyer = new Buyer(
    'John Doe',
    'example@email.com',
    '1122334455',
  );
```

You can initialize the `$id` and the `$customer_id` if you needed.

```php
use Beebmx\KirbyPay\Elements\Buyer;

$buyer = new Buyer(
    'John Doe',
    'example@email.com',
    '1122334455',
    'cus_x3OkX5uXIJMmKKcNxfJM'
  );
```

:::tip
The `$customer_id` attribute is usually required by the driver integration. If you do not initialize it, it will be equals to the `$id`. 
:::

### toArray
- params: `none`
- returns: `array`

Convert the `Buyer` object into an array.

```php
$buyer->toArray();
```

## Item

### __constructor
- params: (string `$name`, float `$amount`, int `$quantity`, `$id` = null)

Creates an instance of `Item` element class.

```php
use Beebmx\KirbyPay\Elements\Item;

$item = new Item('Product 01', 300.00, 3);
```

### toArray
- params: `none`
- returns: `array`

Convert the `Item` object into an array.

```php
$item->toArray();
```

## Items

### __constructor
- params: (array [Item](#item) `$items` = [])

Creates an instance of `Items` element class.

```php
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;

$items = new Items([
  new Item('Product 01', 200.00, 1),
  new Item('Product 02', 100.00, 3)
]);
```

### toArray
- params: `none`
- returns: `array`

Convert the `Items` object into an array.

```php
$items->toArray();
```
### all
- params: `none`
- returns: `Illuminate\Support\Collection`

Returns an instance of `Illuminate\Support\Collection`.

```php
$items->all();
```

### put
- params: ([Item](#item) `$item`)

Insert a new `Item` in the instance collection.

```php
use Beebmx\KirbyPay\Elements\Item;

$items->put(
    new Item('Product 03', 100.00, 1)
);
```

### totalQuantity
- params: `none`
- returns: `int`

Returns the sum of all [Item](#item) quantity.

```php
$items->totalQuantity();
```

### amount
- params: `none`
- returns: `float`

Returns the sum of all [Item](#item) amounts.

```php
$items->amount();
```

### count
- params: `none`
- returns: `int`

Returns the number of [Item](#item) in the instance.

```php
$items->count();
```

## Extras

### __constructor
- params: (array `$extras` = null)

Creates an instance of `Extras` element class.

```php
use Beebmx\KirbyPay\Elements\Extras;

$extras = new Extras([
  'shipping' => 100,
  'taxes' => 200,
]);
```

### toArray
- params: `none`
- returns: `array`

Convert the `Extras` object into an array.

```php
$items->toArray();
```

### amount
- params: `none`
- returns: `float`

Returns the sum of all `Extras` amounts.

```php
$extras
```

### count
- params: `none`
- returns: `int`

Returns the number of `Extras` in the collection.

```php
$extras->count();
```

## Shipping

### __constructor
- params: (string `$address`, string `$postal_code` = null, string `$city` = null, string `$state` = null, string `$country` = null)

Creates an instance of `Shipping` element class.

```php
use Beebmx\KirbyPay\Elements\Shipping;

$shipping = new Shipping(
    'Know address 123',
    '12345',
    'City',
    'State',
    'US'
  );
```

### toArray
- params: `none`
- returns: `array`

Convert the `Shipping` object into an array.

```php
$shipping->toArray();
```

## Source

### __constructor
- params: (`$id`, string `$name`, `$last4`, `$type` = null, `$brand` = null)

Creates an instance of `Source` element class.

```php
use Beebmx\KirbyPay\Elements\Source;

$source = new Source(
    'src_l3JaVVXi1j4OqI2jIxoO',
    'John Doe',
    '3512',
    'card',
    'visa'
);
```

### toArray
- params: `none`
- returns: `array`

Convert the `Source` object into an array.

```php
$source->toArray();
```

## Customer

### __constructor
- params: (`$id`, `$email`, [Buyer](#buyer) `$buyer`, [Source](#source) `$source`)

Creates an instance of `Customer` element class.

```php
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Customer;
use Beebmx\KirbyPay\Elements\Source;

$customer = new Customer(
    'John Doe',
    'example@email.com',
    new Buyer(
        'John Doe',
        'example@email.com',
        '1122334455',
    ),
    new Source(
        'src_l3JaVVXi1j4OqI2jIxoO',
        'John Doe',
        '3512',
        'card',
        'visa'
    )
);
```

### toArray
- params: `none`
- returns: `array`

Convert the `Customer` object into an array.

```php
$customer->toArray();
```

## Order

### __constructor
- params: (`$id`, string `$status`, [Buyer](#buyer) `$customer`, [Items](#items) `$items`, [Shipping](#shipping) `$shipping` = null, array `$extra` = null)

Creates an instance of `Order` element class.

```php
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order;

$order = new Order(
    'ord_tPETUy1KHNNiSNHHIjPc',
    'paid',
    new Buyer(
        'John Doe',
        'example@email.com',
        '1122334455',
    ),
    new Items([
      new Item('Product 01', 200.00, 1),
      new Item('Product 02', 100.00, 3)
    ])
);
```

### toArray
- params: `none`
- returns: `array`

Convert the `Order` object into an array.

```php
$order->toArray();
```

## Charge

### __constructor
- params: (`$id`, string `$status`, [Buyer](#buyer) `$customer`, [Items](#items) `$items`, [Shipping](#shipping) `$shipping` = null, array `$extra` = null)

Creates an instance of `Charge` element class.

```php
use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Charge;

$charge = new Charge(
    'ord_tPETUy1KHNNiSNHHIjPc',
    'paid',
    new Buyer(
        'John Doe',
        'example@email.com',
        '1122334455',
    ),
    new Items([
      new Item('Product 01', 200.00, 1),
      new Item('Product 02', 100.00, 3)
    ])
);
```

### toArray
- params: `none`
- returns: `array`

Convert the `Charge` object into an array.

```php
$charge->toArray();
```

# Snippets

## Payments

````php
<?php
  snippet('kirby-pay.payment', [
    'items' => [
      ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1, 'id' => '00001'],
      ['name' => 'Product 002', 'amount' => 50.00, 'quantity' => 2, 'id' => '00002'],
    ],
    'customer' => [
      'name' => 'John Doe',
      'email' => 'john@doe.com',
      'phone' => '3311223344',
    ],
    'shipping' => [
      'address' => 'Dirección conocida 123',
      'state' => 'Jalisco',
      'city' => 'Guadalajara',
      'postal_code' => '44000',
    ],
    'card' => [
      'name' => 'John Doe',
      'number' => '4242424242424242',
      'month' => '12',
      'year' => '23',
      'cvc' => '123',
    ],
  ])
?>
````

## Order

````php
<?php
  snippet('kirby-pay.order', [
    'uuid' => $customer->uuid,
    'items' => [
      ['name' => 'Product 001', 'amount' => 300.00, 'quantity' => 1, 'id' => '00001'],
      ['name' => 'Product 002', 'amount' => 50.00, 'quantity' => 2, 'id' => '00002'],
    ],
    'shipping' => [
      'address' => 'Dirección conocida 123',
      'state' => 'Jalisco',
      'city' => 'Guadalajara',
      'postal_code' => '44000',
    ],
  ])
?>
````

## Customer

### Create

````php
<?php
    snippet('kirby-pay.customer', [
      'customer' => [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'phone' => '3311223344',
      ],
      'card' => [
        'name' => 'John Doe',
        'number' => '4242424242424242',
        'month' => '12',
        'year' => '23',
        'cvc' => '123',
      ],
    ])
?>
````

### Update

```php
<?php
    snippet('kirby-pay.customer.update', [
      'uuid' => $customer->uuid,
      'customer' => [
        'name' => $customer->customer['name'],
        'email' => $customer->customer['email'],
        'phone' => $customer->customer['phone'],
      ],
    ])
?>
```

### Update payment source

````php
<?php
    snippet('kirby-pay.customer.source', [
      'uuid' => $customer->uuid,
      'card' => [
        'name' => 'John Doe',
        'number' => '5555555555554444',
        'month' => '12',
        'year' => '23',
        'cvc' => '123',
      ],
    ])
?>
````
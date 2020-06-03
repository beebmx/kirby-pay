# Customer

## Definition

The `Customer` class inside of it, has a connection with the service or `driver` selected in your Kirby's `config.php` file, and as the name describes, it performs the customer management of the package.

The `Customer` class is part of the [Resource](resource) object and you can use all these methods to improve functionallity.

## Methods

### create
- params: ([Buyer](elements.md#buyer) `$customer`, string `$token`, string `$type`) 
- returns: `Customer`

This `static` method creates a new `Customer` resource.

```php
use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Buyer;

Customer::create(
  new Buyer(
    'John Doe',
    'example@email.com',
    '1122334455',
  ),
  'token',
  'card'
);
```

### firstOrCreate
- params: ([Buyer](elements.md#buyer) `$customer`, string `$token`, string `$type`) 
- returns: `Customer`

This `static` method creates a new `Customer` resource if it doesn't exist, and if it exists returns an instance of it.
It will search by the `Customer`'s email field.

```php
use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Buyer;

Customer::firstOrCreate(
  new Buyer(
    'John Doe',
    'example@email.com',
    '1122334455',
  ),
  'token',
  'card'
);
```

### update
- params: ([Buyer](elements.md#buyer) `$customer`) 
- returns: `Customer`

This method updates a `Customer` resource.

```php
use Beebmx\KirbyPay\Customer;
use Beebmx\KirbyPay\Elements\Buyer;

$customer = Customer::find('ae9c2dda-9102-4202-ae3b-1c62da2e03bc');

$customer->update(
  new Buyer(
    'Jane Doe',
    'example@email.com',
    '1122334455',
    $customer->id
  )
);
```

### updateSource
- params: (string `token`) 
- returns: `Customer`

This method updates the payment source in a `Customer` resource.

```php
use Beebmx\KirbyPay\Customer;

$customer = Customer::find('ae9c2dda-9102-4202-ae3b-1c62da2e03bc');
$customer->updateSource('new-token');
```

### remove
- params: `none` 
- returns: `bool`

This method deletes a `Customer` resource.

```php
use Beebmx\KirbyPay\Customer;

$customer = Customer::find('ae9c2dda-9102-4202-ae3b-1c62da2e03bc');
$customer->remove();
```

### email
- params: (string `$email`) 
- returns: `Customer`|`bool`

This `static` method searches for a `Customer` resource by the `email` field.
If not found, returns `false`

```php
use Beebmx\KirbyPay\Customer;

Customer::email('example@email.com');
```

### serviceUrl
- params: `none`
- returns: `string`

This `static` method returns the URL of the customer in the `driver` service.

```php
use Beebmx\KirbyPay\Customer;

Customer::serviceUrl();
```
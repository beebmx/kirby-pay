# Customer

## Definition

The `Customer` class inside of it, has a connection with the service or `driver` selected in your `config.php` file, and as the name describes, it performs the customer management of the package.

The `Customer` class is part of the [Resource](resource) object, you can use all of these methods to improve the functionallity.

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

This `static` method creates a new `Customer` resource if not exist, and if exists return an instance of it.
It will search with the email of the `Customer`.

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

This method updates the source payment in a `Customer` resource.

```php
use Beebmx\KirbyPay\Customer;

$customer = Customer::find('ae9c2dda-9102-4202-ae3b-1c62da2e03bc');
$customer->updateSource('new-token');
```

### remove
- params: `none` 
- returns: `bool`

This method delete a `Customer` resource.

```php
use Beebmx\KirbyPay\Customer;

$customer = Customer::find('ae9c2dda-9102-4202-ae3b-1c62da2e03bc');
$customer->remove();
```

### email
- params: (string `$email`) 
- returns: `Customer`|`bool`

This `static` method searches a `Customer` resource with the `email`.
If not found, returns `false`

```php
use Beebmx\KirbyPay\Customer;

Customer::email('example@email.com');
```

### serviceUrl
- params: `none`
- returns: `string`

This `static` method returns the URL of the customers in the `driver` service.

```php
use Beebmx\KirbyPay\Customer;

Customer::serviceUrl();
```
# Resource

## Definition

[Payment](payment), [Customer](customer) and [Log](log) have an instance of `resource`.
That means, you can use the `resource` methods in those classes.

## Methods

### find
- params: (`$value`) 
- returns: `instance`|`bool`

You can find an `instance` of an `object` with a *string* of `uuid` or with a `pay_id`.
If the `resource` does not found anything, returns `false`

```php
use Beebmx\KirbyPay\Customer;

//Should be a valid uuid
Customer::find('4f139ef0-484a-4749-a51f-5ffe339dd1c1');

//Should be a valid pay_id
Customer::find(1);
```

### first
- params: `none` 
- returns: `instance`|`bool`

Returns an `instance` of the first element created.
If the `resource` does not found anything, returns `false`

```php
use Beebmx\KirbyPay\Customer;

Customer::first();
```

:::tip
The order to sort the resources is chronologically inverse, that means, it's the last created.
:::

### last
- params: `none` 
- returns: `instance`|`bool`

Returns an `instance` of the last element created.
If the `resource` does not found anything, returns `false`

```php
use Beebmx\KirbyPay\Customer;

Customer::last();
```

:::tip
The order to sort the resources is chronologically inverse, that means, it's the first created.
:::

### write
- params: (array `$attributes`, int `$pay_id` = *null*, string `$uuid` = *null*) 
- returns: `instance`

Returns an `instance` of the element created.
If you does not send a `$pay_id` and a `$uuid` it creates a new resource, otherwise it will rewrite the existing one.

```php
use Beebmx\KirbyPay\Customer;

Customer::write([
    'name' => 'John Doe',
    'email' => 'email@example.com'
]);
```

### read
- params: (string `$file`) 
- returns: `Illuminate\Support\Collection`

Returns an instance `Illuminate\Support\Collection` of the element.

```php
use Beebmx\KirbyPay\Customer;

Customer::read('1-4f139ef0-484a-4749-a51f-5ffe339dd1c1.json');
```

### destroy
- params: (int `$pay_id`, string `$uuid`) 
- returns: `bool`

Returns a boolean if the resource was successfully deleted.

```php
use Beebmx\KirbyPay\Customer;

Customer::destroy(1, '4f139ef0-484a-4749-a51f-5ffe339dd1c1.json');
```

### get
- params: `none` 
- returns: `array`

Returns an array with all the resources.

```php
use Beebmx\KirbyPay\Customer;

Customer::get();
```

### take
- params: (int `$number` = 10) 
- returns: `Resource`

Take only the `$number` of elements in the resource.

Returns an instance of the resource.

```php
use Beebmx\KirbyPay\Customer;

Customer::take()->get();
```


### skip
- params: (int `$number` = 10) 
- returns: `Resource`

Skip the `$number` of elements in the resource.

Returns an instance of the resource.

```php
use Beebmx\KirbyPay\Customer;

Customer::skip()->get();
```

### search
- params: (int `$number` = 10) 
- returns: `Resource`

Search elements in the resource one or more parameters.

Returns an instance of the resource.

If you search with only one parameter.

```php
use Beebmx\KirbyPay\Customer;

Customer::search(1, 'pay_id')->get();
Customer::search('4f139ef0-484a-4749-a51f-5ffe339dd1c1', 'uuid')->get();
Customer::search('jane', 'email')->get();
```

If you search with more than one parameter.

```php
use Beebmx\KirbyPay\Customer;

Customer::search('26', 'pay_id|uuid')->get();
```

### page
- params: (int `$page` = 1, int `$perPage` = 10) 
- returns: `Resource`

Paginate elements int the resource.

Returns an instance of the resource.

```php
use Beebmx\KirbyPay\Customer;

Customer::page()->get();
```

### diffForHumans
- params: `none` 
- returns: `Resource`

Transform all the dates (`created_at` and `updated_at`) to a human format.

Returns an instance of the resource.

```php
use Beebmx\KirbyPay\Customer;

Customer::diffForHumans()->get();
```

### setSort
- params: (string `$sort` = 'desc') 
- returns: `Resource`

Set the direction of the elements sorting of the resource.

Returns an instance of the resource.

```php
use Beebmx\KirbyPay\Customer;

Customer::setSort('asc')->get();
```

### count
- params: `none` 
- returns: `int`

Returns the number of the elements in the resource.

```php
use Beebmx\KirbyPay\Customer;

Customer::count();
```

### isEmpty
- params: `none` 
- returns: `bool`

Returns a boolean if the resource is empty.

```php
use Beebmx\KirbyPay\Customer;

Customer::isEmpty();
```

### isNotEmpty
- params: `none` 
- returns: `bool`

Returns a boolean if the resource is not empty.

```php
use Beebmx\KirbyPay\Customer;

Customer::isNotEmpty();
```

### getPath
- params: `none` 
- returns: `string`

Returns a string with the name path of the resource.

```php
use Beebmx\KirbyPay\Customer;

Customer::getPath();
```
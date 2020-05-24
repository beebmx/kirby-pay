<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Exception\UpdateCustomerException;

class Customer extends Model
{
    protected static $path = 'customer';

    public static function create(Buyer $customer, $token, $method = null)
    {
        return static::write(
            static::driver()->createCustomer($customer, $token, $method)->toArray()
        );
    }

    public function update(Buyer $customer)
    {
        if (!$this->hasCustomerInitialized()) {
            throw new UpdateCustomerException('A valid customer is requred');
        }

        $this->customer = $customer->toArray();

        if (static::driver()->updateCustomer($this)) {
            $this->email = $this->customer['email'];
            $this->save();
        }

        return $this;
    }

    public function updateSource(string $token)
    {
        if (!$this->hasCustomerInitialized()) {
            throw new UpdateCustomerException('A valid customer is requred');
        }

        if ($source = static::driver()->updateCustomerSource($this, $token)) {
            $this->source = $source->toArray();
            $this->save();
        }

        return $this;
    }

    public function remove(): bool
    {
        if (!$this->hasCustomerInitialized()) {
            throw new UpdateCustomerException('A valid customer is requred');
        }

        if (static::driver()->deleteCustomer($this)) {
            return $this->delete();
        }

        return false;
    }

    public static function firstOrCreate(Buyer $promise, $token, $method = null)
    {
        if ($found = static::email($promise->email)) {
            return $found;
        }

        return static::create($promise, $token, $method);
    }

    public static function email(string $email)
    {
        return static::search($email, 'email')->first();
    }

    public static function serviceUrl(): string
    {
        return static::driver()->getUrls()['customers'];
    }

    protected function hasCustomerInitialized()
    {
        return $this->id && $this->pay_id && $this->uuid;
    }
}

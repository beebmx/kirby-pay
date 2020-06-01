<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Exception\UpdateCustomerException;

class Customer extends Model
{
    /**
     * Path of the Customer Model
     *
     * @var string
     */
    protected static $path = 'customer';

    /**
     * Create a file and Customer instance with given data
     *
     * @param Buyer $customer
     * @param $token
     * @param null $method
     * @return Customer
     */
    public static function create(Buyer $customer, $token, $method = null)
    {
        return static::write(
            static::driver()->createCustomer($customer, $token, $method)->toArray()
        );
    }

    /**
     * Update the Customer data
     *
     * @param Buyer $customer
     * @return $this
     * @throws UpdateCustomerException
     */
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

    /**
     * Update the Customer source
     *
     * @param string $token
     * @return $this
     * @throws UpdateCustomerException
     */
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

    /**
     * Delete the current Customer
     *
     * @return bool
     * @throws UpdateCustomerException
     */
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

    /**
     * Find or create a Customer with an email
     *
     * @param Buyer $promise
     * @param $token
     * @param null $method
     * @return Customer
     */
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

    /**
     * Get the service customer URL
     *
     * @return string
     */
    public static function serviceUrl(): string
    {
        return static::driver()->getUrls()['customers'];
    }

    /**
     * Validate if the Customer has been initialized
     *
     * @return bool
     */
    protected function hasCustomerInitialized()
    {
        return $this->id && $this->pay_id && $this->uuid;
    }
}

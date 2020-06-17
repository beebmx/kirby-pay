<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Extras;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Shipping;
use Illuminate\Support\Collection;

class Payment extends Model
{
    /**
     * Path of the Payment Model
     *
     * @var string
     */
    protected static $path = 'payment';

    /**
     * Create a payment order with a given customer
     *
     * @param Customer $customer
     * @param Items|Collection $items_to_sell
     * @param Extras|Collection|null $extra_amounts
     * @param string $type
     * @param Shipping|Collection|null $shipping_instructions
     * @return Payment
     */
    public static function orderWithCustomer(Customer $customer, $items_to_sell, $extra_amounts = null, string $type = 'card', $shipping_instructions = null)
    {
        $items = static::getItems($items_to_sell);
        $extras = static::getExtras($extra_amounts);
        $shipping = static::getShipping($shipping_instructions);

        return static::write(
            static::driver()->createOrder($customer, $items, $extras, $type, $shipping)->toArray()
        );
    }

    /**
     * Creates a payment order
     *
     * @param Buyer|Collection $customer
     * @param Items|Collection $items_to_sell
     * @param Extras|Collection|null $extra_amounts
     * @param string|null $token
     * @param string $type
     * @param Shipping|Collection|null $shipping_instructions
     * @return Payment
     */
    public static function order($customer, $items_to_sell, $extra_amounts = null, string $token = null, string $type = 'card', $shipping_instructions = null)
    {
        $buyer = static::getBuyer($customer);
        $items = static::getItems($items_to_sell);
        $extras = static::getExtras($extra_amounts);
        $shipping = static::getShipping($shipping_instructions);
        $customer = Customer::firstOrCreate($buyer, $token, $type);

        return static::orderWithCustomer($customer, $items, $extras, $type, $shipping);
    }

    /**
     * Creates a payment charge
     *
     * @param Buyer|Collection $customer
     * @param Items|Collection $items_to_sell
     * @param Extras|Collection|null $extra_amounts
     * @param string|null $token
     * @param string $type
     * @param Shipping|Collection|null $shipping_instructions
     * @return Payment
     */
    public static function charge($customer, $items_to_sell, $extra_amounts = null, string $token = null, string $type = 'card', $shipping_instructions = null)
    {
        $buyer = static::getBuyer($customer);
        $items = static::getItems($items_to_sell);
        $extras = static::getExtras($extra_amounts);
        $shipping = static::getShipping($shipping_instructions);

        return static::write(
            static::driver()->createCharge($buyer, $items, $extras, $token, $type, $shipping)->toArray()
        );
    }

    /**
     * Get the service payment URL
     *
     * @return string
     */
    public static function serviceUrl(): string
    {
        return static::driver()->getUrls()['payments'];
    }

    /**
     * Get all the payment methods available in the service
     *
     * @return array
     */
    public static function getPaymentMethods(): array
    {
        return static::driver()->getPaymentMethods();
    }

    /**
     * Parse the amount of the service value
     *
     * @param $amount
     * @return float
     */
    public static function parseAmount($amount): float
    {
        return static::driver()->parsePrice($amount);
    }

    /**
     * Get an instance of buyer
     *
     * @param Buyer|Collection $customer
     * @return Buyer
     */
    protected static function getBuyer($customer): Buyer
    {
        return $customer instanceof Buyer
            ? $customer
            : static::setBuyer($customer);
    }

    /**
     * Get an instance of buyer through Collection
     *
     * @param Collection $customer
     * @return Buyer
     */
    protected static function setBuyer(Collection $customer): Buyer
    {
        return new Buyer(
            $customer['name'],
            $customer['email'],
            $customer['phone'] ?? null,
            $customer['id'] ?? null,
        );
    }

    /**
     * Get an instance of Items
     *
     * @param Items|Collection $items
     * @return Items
     */
    protected static function getItems($items): Items
    {
        return $items instanceof Items
            ? $items
            : static::setItems($items);
    }

    /**
     * Get an instance of Extras
     *
     * @param Extras|Collection|null $extras
     * @return Extras|null
     */
    protected static function getExtras($extras)
    {
        if (!$extras) {
            return null;
        }

        return $extras instanceof Extras
            ? $extras
            : new Extras($extras->toArray() ?? null);
    }

    /**
     * Get an instance of Items through Collection
     *
     * @param Collection $elements
     * @return Items
     */
    protected static function setItems(Collection $elements): Items
    {
        $items = new Items;

        $elements->each(function ($element) use ($items) {
            $items->put(new Item(
                $element['name'],
                $element['amount'],
                $element['quantity'],
                $element['id'] ?? null,
            ));
        });

        return $items;
    }

    /**
     * Get an instance of Shipping
     *
     * @param Shipping|Collection $shipping
     * @return Shipping|null
     */
    protected static function getShipping($shipping)
    {
        return $shipping instanceof Shipping
            ? $shipping
            : static::setShipping($shipping);
    }

    /**
     * Get an instance of Shipping through Collection
     *
     * @param Collection|null $shipping
     * @return Shipping|null
     */
    protected static function setShipping(Collection $shipping = null)
    {
        if (!$shipping) {
            return null;
        }

        return new Shipping(
            $shipping['address'],
            $shipping['postal_code'] ?? null,
            $shipping['city'] ?? null,
            $shipping['state'] ?? null,
            $shipping['country'] ?? null,
        );
    }
}

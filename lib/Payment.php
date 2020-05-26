<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Shipping;
use Illuminate\Support\Collection;

class Payment extends Model
{
    protected static $path = 'payment';

    public static function orderWithCustomer(Customer $customer, $items_to_sell, string $type = 'card', $shipping_instructions = null)
    {
        $items = static::getItems($items_to_sell);
        $shipping = static::getShipping($shipping_instructions);

        return static::write(
            static::driver()->createOrder($customer, $items, $type, $shipping)->toArray()
        );
    }

    public static function order(Collection $customer, Collection $items_to_sell, string $token = null, string $type = 'card', Collection $shipping_instructions = null)
    {
        $buyer = static::setBuyer($customer);
        $items = static::setItems($items_to_sell);
        $shipping = static::setShipping($shipping_instructions);
        $customer = Customer::firstOrCreate($buyer, $token, $type);

        return static::orderWithCustomer($customer, $items, $type, $shipping);
    }

    public static function charge(Collection $customer, Collection $items_to_sell, string $token = null, string $type = 'card', Collection $shipping_instructions = null)
    {
        $buyer = static::setBuyer($customer);
        $items = static::setItems($items_to_sell);
        $shipping = static::setShipping($shipping_instructions);

        return static::write(
            static::driver()->createCharge($buyer, $items, $token, $type, $shipping)->toArray()
        );
    }

    public static function serviceUrl(): string
    {
        return static::driver()->getUrls()['payments'];
    }

    public static function getPaymentMethods(): array
    {
        return static::driver()->getPaymentMethods();
    }

    public static function parseAmount($amount)
    {
        return static::driver()->parsePrice($amount);
    }

    protected static function setBuyer(Collection $customer): Buyer
    {
        return new Buyer(
            $customer['name'],
            $customer['email'],
            $customer['phone'] ?? null,
            $customer['id'] ?? null,
        );
    }

    protected static function getItems($items): Items
    {
        return $items instanceof Items
            ? $items
            : static::setItems($items);
    }

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

    protected static function getShipping($shipping)
    {
        return $shipping instanceof Shipping
            ? $shipping
            : static::setShipping($shipping);
    }

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

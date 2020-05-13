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

    protected static $type = '.json';

    public static function order(Collection $customer, Collection $items, string $token = null, string $type = 'card', Collection $shipping = null)
    {
        $customer = Customer::firstOrCreate($customer, $token, $type);

        return static::write(
            static::driver()->createOrder(new Collection($customer), $items, $token, $type, $shipping)
        );
    }

    public static function charge(Collection $customer, Collection $items, string $token = null, string $type = 'card', Collection $shipping = null)
    {
        $buyer = static::setBuyer($customer);
        $items = static::setItems($items);
        $shipping = static::setShipping($shipping);

        return static::write(
            static::driver()->createCharge($buyer, $items, $token, $type, $shipping)->toArray()
        );
    }

    public static function serviceUrl()
    {
        return static::driver()->getUrls()['payments'];
    }

    public static function getPaymentMethods()
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
        );
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

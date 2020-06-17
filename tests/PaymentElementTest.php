<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Elements\Buyer;
use Beebmx\KirbyPay\Elements\Charge;
use Beebmx\KirbyPay\Elements\Extras;
use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\Items;
use Beebmx\KirbyPay\Elements\Order;
use Illuminate\Support\Str;

class PaymentElementTest extends TestCase
{
    /** @test */
    public function an_order_calculates_the_amount_of_the_items()
    {
        $order = new Order(
            $id = 'ord_' . Str::random(20),
            'paid',
            new Buyer(
                'Jane Doe',
                'jane@mail.com',
                '1122334455'
            ),
            new Items([
                new Item('Producto 01', 100, 1, 'product-01'),
                new Item('Producto 02', 50, 2, 'product-02'),
                new Item('Producto 03', 200, 1, 'product-03'),
            ])
        );

        $this->assertEquals($id, $order->id);
        $this->assertEquals(400, $order->amount);
    }

    /** @test */
    public function an_order_calculates_the_amount_of_the_items_and_extras()
    {
        $order = new Order(
            $id = 'ord_' . Str::random(20),
            'paid',
            new Buyer(
                'Jane Doe',
                'jane@mail.com',
                '1122334455'
            ),
            new Items([
                new Item('Producto 01', 100, 1, 'product-01'),
                new Item('Producto 02', 50, 2, 'product-02'),
                new Item('Producto 03', 200, 1, 'product-03'),
            ]),
            new Extras([
                'shipping' => 100,
                'taxes' => 40.20,
            ])
        );

        $this->assertEquals($id, $order->id);
        $this->assertEquals(540.20, $order->amount);
    }

    /** @test */
    public function a_charge_calculates_the_amount_of_the_items()
    {
        $charge = new Charge(
            $id = 'ord_' . Str::random(20),
            'paid',
            new Buyer(
                'Jane Doe',
                'jane@mail.com',
                '1122334455'
            ),
            new Items([
                new Item('Producto 01', 100, 1, 'product-01'),
                new Item('Producto 02', 50, 2, 'product-02'),
                new Item('Producto 03', 200, 1, 'product-03'),
            ])
        );

        $this->assertEquals($id, $charge->id);
        $this->assertEquals(400, $charge->amount);
    }

    /** @test */
    public function a_charge_calculates_the_amount_of_the_items_and_extras()
    {
        $charge = new Charge(
            $id = 'ord_' . Str::random(20),
            'paid',
            new Buyer(
                'Jane Doe',
                'jane@mail.com',
                '1122334455'
            ),
            new Items([
                new Item('Producto 01', 100, 1, 'product-01'),
                new Item('Producto 02', 50, 2, 'product-02'),
                new Item('Producto 03', 200, 1, 'product-03'),
            ]),
            new Extras([
                'shipping' => 100,
                'taxes' => 40.20,
            ])
        );

        $this->assertEquals($id, $charge->id);
        $this->assertEquals(540.20, $charge->amount);
    }
}

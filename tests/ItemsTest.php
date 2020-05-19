<?php


namespace Beebmx\KirbyPay\Tests;


use Beebmx\KirbyPay\Elements\Item;
use Beebmx\KirbyPay\Elements\ItemException;
use Beebmx\KirbyPay\Elements\Items;

class ItemsTest extends TestCase
{
    /** @test */
    public function it_create_items_object_from_constructor()
    {
        $items = new Items([
           new Item('Producto 01', 100, 1, 'product-01'),
           new Item('Producto 02', 100, 1, 'product-02'),
        ]);

        $this->assertInstanceOf(Items::class, $items);
        $this->assertCount(2, $items->toArray());
    }

    /** @test */
    public function it_throw_an_error_if_an_item_is_not_an_instance_of_item()
    {
        $this->expectException(ItemException::class);

        new Items([
            ['Producto 01', 100, 1, 'product-01'],
            ['Producto 02', 100, 1, 'product-02'],
        ]);
    }

    /** @test */
    public function it_creates_an_empty_items_object_and_added_later_with_item()
    {
        $items = new Items;
        $this->assertInstanceOf(Items::class, $items);
        $this->assertCount(0, $items->toArray());

        $items->put(new Item('Producto 01', 100, 1, 'product-01'));
        $items->put(new Item('Producto 02', 100, 1, 'product-02'));
        $items->put(new Item('Producto 03', 100, 1, 'product-03'));
        $this->assertCount(3, $items->toArray());
    }
}
<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Elements\Extras;
use Beebmx\KirbyPay\Exception\ExtraException;

class ExtrasElementsTest extends TestCase
{
    /** @test */
    public function it_create_extras_object_with_empty_constructor()
    {
        $extras = new Extras;

        $this->assertInstanceOf(Extras::class, $extras);
        $this->assertCount(0, $extras->toArray());
    }

    /** @test */
    public function it_create_extras_object_from_constructor()
    {
        $extras = new Extras([
            'shipping' => 100,
            'taxes' => 40.20,
        ]);

        $this->assertInstanceOf(Extras::class, $extras);
        $this->assertCount(2, $extras->toArray());
    }

    /** @test */
    public function it_throw_an_error_if_an_item_is_not_an_instance_of_item()
    {
        $this->expectException(ExtraException::class);

        new Extras([
            'shipping' => 100,
            'taxes' => 'invalid-value',
        ]);
    }

    /** @test */
    public function it_returns_an_array_with_extras_element()
    {
        $extras = new Extras([
            'shipping' => 100,
            'taxes' => 40.20,
        ]);

        $this->assertIsArray($extras->toArray());
    }

    /** @test */
    public function it_counts_all_extras()
    {
        $extras = new Extras([
            'shipping' => 100,
            'taxes' => 40.20,
        ]);

        $this->assertEquals(2, $extras->count());
    }

    /** @test */
    public function it_sum_all_amounts_of_items()
    {
        $extras = new Extras([
            'shipping' => 100,
            'taxes' => 40.20,
        ]);

        $this->assertEquals(140.20, $extras->amount());

        $extras = new Extras;

        $this->assertEquals(0, $extras->amount());
    }
}

<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Kirby\Cms\App;
use Kirby\Toolkit\Dir;

class ResourceTest extends TestCase
{
    public $kirby;

    public $model;

    public function setUp(): void
    {
        $this->model = new class extends Model {
            protected static $path = 'resource';

            public static function serviceUrl(): string
            {
                return 'https://foo.bar';
            }
        };

        $this->kirby = new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ]
        ]);
    }

    public function tearDown(): void
    {
        Dir::remove(__DIR__ . '/tmp/resource');
    }

    /** @test */
    public function a_resource_return_their_own_path()
    {
        $this->assertEquals('resource', $this->model::getPath());
    }

    /** @test */
    public function a_resource_knows_if_has_an_own_directory_empty()
    {
        $this->assertTrue($this->model::isEmpty());
        touch(option('beebmx.kirby-pay.storage') . "/{$this->model::getPath()}/file-02");
        $this->assertFalse($this->model::isEmpty());
    }

    /** @test */
    public function a_resource_can__write_a_new_model_object()
    {
        $this->assertTrue($this->model::isEmpty());
        $file = $this->model::write([]);
        $this->assertFalse($this->model::isEmpty());
    }

    /** @test */
    public function a_resource_create_a_filename_of_pay_id_and_uuid()
    {
        $file = $this->model::write([]);

        $this->assertTrue(
            file_exists(option('beebmx.kirby-pay.storage') . "/{$this->model::getPath()}/{$file->pay_id}-{$file->uuid}.json")
        );
    }

    /** @test */
    public function a_resource_always_write_pay_id_and_uuid_with_timestamps()
    {
        $file = $this->model::write([]);

        $this->assertIsInt($file->pay_id);
        $this->assertTrue(Str::isUuid($file->uuid));
        $this->assertInstanceOf(Carbon::class, $file->created_at);
        $this->assertInstanceOf(Carbon::class, $file->updated_at);
    }

    /** @test */
    public function a_new_resource_set_pay_id_as_autoincrement_int()
    {
        $this->assertTrue($this->model::isEmpty());

        $this->model::write([]);            //pay_id = 1
        $this->model::write([]);            //pay_id = 2
        $file = $this->model::write([]);    //pay_id = 3

        $this->assertEquals(3, $file->pay_id);
    }

    /** @test */
    public function a_resource_can_read_all_the_attributes()
    {
        $this->model::write(['foo' => 'bar', 'bar' => 'baz']);

        $object = $this->model::first();

        $this->assertEquals('bar', $object->foo);
        $this->assertEquals('baz', $object->bar);
    }

    /** @test */
    public function a_resource_returns_the_first_and_last_element_and_sort_resource()
    {
        $this->model::write([]);
        $this->model::write([]);
        $this->model::write([]);

        $this->assertEquals(3, $this->model::first()->pay_id);
        $this->assertEquals(1, $this->model::last()->pay_id);
        $this->assertEquals(1, $this->model::setSort('asc')->first()->pay_id);
        $this->assertEquals(3, $this->model::setSort('asc')->last()->pay_id);
    }

    /** @test */
    public function a_resource_returns_the_number_of_elements()
    {
        $this->model::write([]);
        $this->model::write([]);
        $this->model::write([]);

        $this->assertEquals(3, $this->model::count());
    }

    /** @test */
    public function a_resource_can_be_found_by_pay_id_or_uuid()
    {
        $resource1 = $this->model::write([]);
        $resource2 = $this->model::write([]);

        $this->assertEquals($resource1, $this->model::find(1));
        $this->assertEquals($resource2, $this->model::find($resource2->uuid));
        $this->assertEquals(false, $this->model::find(3));
    }

    /** @test */
    public function a_resource_can_be_read_with_cast()
    {
        $object = $this->model::write(['amount' => 200]);
        $file = "{$object->pay_id}-{$object->uuid}.json";

        $resource = $this->model::read($file);

        $this->assertNotNull($resource);
        $this->assertEquals($object->pay_id, $resource['pay_id']);
        $this->assertEquals('$200.00', $resource['amount']);
    }

    /** @test */
    public function a_resource_get_all_the_instances()
    {
        $this->model::write([]);
        $this->model::write([]);
        $this->model::write([]);

        $this->assertIsArray($this->model::get());
        $this->assertCount(3, $this->model::get());
    }

    /** @test */
    public function a_resource_can_take_a_number_of_instances()
    {
        for($i = 0; $i < 25; $i++) {
            $this->model::write([]);
        }

        $this->assertCount(25, $this->model::get());
        $this->assertCount(10, $this->model::take()->get());
        $this->assertCount(5, $this->model::take(5)->get());
    }

    /** @test */
    public function a_resource_can_skip_a_number_of_instances()
    {
        for($i = 0; $i < 25; $i++) {
            $this->model::write([]);
        }

        $this->assertCount(25, $this->model::get());
        $this->assertEquals(15, $this->model::skip()->first()->pay_id);
        $this->assertEquals(20, $this->model::skip(5)->first()->pay_id);
    }

    /** @test */
    public function a_resource_can_paginate_a_number_of_instances()
    {
        for($i = 0; $i < 25; $i++) {
            $this->model::write([]);
        }

        $this->assertCount(10, $this->model::page()->get());
        $this->assertEquals(25, $this->model::page()->first()->pay_id);
        $this->assertCount(10, $this->model::page(2)->get());
        $this->assertEquals(15, $this->model::page(2)->first()->pay_id);
        $this->assertCount(5, $this->model::page(3)->get());
        $this->assertEquals(5, $this->model::page(3)->first()->pay_id);

        $this->assertCount(5, $this->model::page(2, 5)->get());
        $this->assertEquals(20, $this->model::page(2, 5)->first()->pay_id);
        $this->assertCount(5, $this->model::page(3, 5)->get());
        $this->assertEquals(15, $this->model::page(3, 5)->first()->pay_id);
    }

    /** @test */
    public function a_resource_can_be_search_with_an_attribute()
    {
        $john = $this->model::write(['email' => 'john@doe.com']);
        $jane = $this->model::write(['email' => 'jane@doe.com']);
        $this->model::write(['email' => 'mail@example.com']);

        $this->assertEquals($john, $this->model::search('john@doe.com', 'email')->first());
        $this->assertEquals($jane, $this->model::search('jane@doe.com', 'email')->first());
        $this->assertCount(1, $this->model::search('john@doe.com', 'email')->get());
        $this->assertCount(1, $this->model::search('jane@doe.com', 'email')->get());
        $this->assertCount(2, $this->model::search('@doe.com', 'email')->get());
        $this->assertCount(1, $this->model::search('@example.com', 'email')->get());
    }

    /** @test */
    public function a_resource_can_be_search_with_multiples_attributes()
    {
        $john = $this->model::write(['email' => 'john@doe.com', 'name' => 'john', 'lastname' => 'doe']);
        $jane = $this->model::write(['email' => 'jane@doe.com', 'name' => 'jane', 'lastname' => 'doe']);
        $this->model::write(['email' => 'mail@example.com']);

        $this->assertCount(2, $this->model::search('@doe.com', 'email')->get());
        $this->assertCount(2, $this->model::search('doe', 'lastname')->get());
        $this->assertCount(2, $this->model::search('doe', 'email|lastname')->get());
        $this->assertCount(1, $this->model::search('john', 'email|lastname')->get());
    }

    /** @test */
    public function a_resource_can_be_search_with_cast_attribute()
    {
        $this->model::write(['email' => 'john@doe.com']);
        $this->model::write(['email' => 'jane@doe.com']);
        $example = $this->model::write(['email' => 'mail@example.com']);

        $this->assertCount(1, $this->model::search(3, 'pay_id:int')->get());
    }
}

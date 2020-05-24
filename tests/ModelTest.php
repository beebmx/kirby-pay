<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Model;
use Kirby\Cms\App;
use Kirby\Toolkit\Dir;

class ModelTest extends TestCase
{
    public $model;

    public function setUp(): void
    {
        $this->model = new class extends Model {
            protected static $path = 'test';

            public static function serviceUrl(): string
            {
                return 'https://foo.bar';
            }
        };
    }

    /** @test */
    public function a_class_can_extends_model_structure()
    {
        $this->assertEquals('https://foo.bar', $this->model::serviceUrl());
    }

    /** @test */
    public function a_model_can_be_initialize_with_some_attributes()
    {
        $object = new $this->model(['foo' => 'bar']);

        $this->assertEquals('https://foo.bar', $object::serviceUrl());
    }

    /** @test */
    public function a_model_return_all_the_attributes_defined()
    {
        $attributes = [
            'pay_id' => null,
            'uuid' => null,
            'foo' => 'bar',
            'bar' => 'baz',
        ];
        $object = new $this->model($attributes);

        $this->assertEquals($attributes, $object->getAttributes());
    }

    /** @test */
    public function a_model_return_an_attribute_dynamically()
    {
        $object = new $this->model([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $this->assertEquals('bar', $object->foo);
        $this->assertEquals('baz', $object->bar);
    }

    /** @test */
    public function a_model_return_an_attribute_as_array()
    {
        $object = new $this->model([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);

        $this->assertEquals('bar', $object['foo']);
        $this->assertEquals('baz', $object['bar']);
    }

    /** @test */
    public function a_model_can_update_an_attribute_dynamically()
    {
        $object = new $this->model(['foo' => 'bar']);
        $object->foo = 'baz';

        $this->assertEquals('baz', $object->foo);
    }

    /** @test */
    public function a_model_can_update_an_attribute_as_array()
    {
        $object = new $this->model(['foo' => 'bar']);
        $object['foo'] = 'baz';

        $this->assertEquals('baz', $object['foo']);
    }

    /** @test */
    public function a_model_returns_all_attributes_as_array()
    {
        $attributes = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];
        $object = new $this->model($attributes);

        $this->assertIsArray($object->toArray());
        $this->assertArrayHasKey('foo', $object->toArray());
        $this->assertArrayHasKey('bar', $object->toArray());
    }

    /** @test */
    public function a_model_returns_all_attributes_as_json()
    {
        $attributes = [
            'pay_id' => null,
            'uuid' => null,
            'foo' => 'bar',
            'bar' => 'baz',
        ];
        $object = new $this->model($attributes);

        $this->assertJson($object->toJson());
        $this->assertEquals(json_encode($attributes), $object->toJson());
    }

    /** @test */
    public function a_model_can_call_any_method_of_resource_like_static_own_method()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ]
        ]);

        $this->assertIsArray($this->model::get());
        $this->assertEquals([], $this->model::get());
    }

    /** @test */
    public function a_model_can_be_save_as_a_new_instance()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ]
        ]);

        $this->model::write([]);
        $instance = new $this->model(['foo' => 'bar', 'bar' => 'baz']);
        $instance->save();

        $this->assertEquals(2, $instance->pay_id);

        Dir::remove(__DIR__ . '/tmp/test');
    }

    /** @test */
    public function a_model_can_be_save_as_a_instance()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ]
        ]);

        $this->model::write([]);
        $instance = $this->model::write(['foo' => 'bar', 'bar' => 'baz']);
        $instance->foo = 'baz';
        $resource = $instance->save();

        $this->assertEquals(2, $resource->pay_id);
        $this->assertEquals('baz', $resource->foo);

        Dir::remove(__DIR__ . '/tmp/test');
    }

    /** @test */
    public function a_model_can_be_deleted()
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ]
        ]);

        $instance = $this->model::write(['foo' => 'bar', 'bar' => 'baz']);
        $this->assertCount(1, $this->model::get());

        $instance->delete();
        $this->assertCount(0, $this->model::get());

        Dir::remove(__DIR__ . '/tmp/test');
    }
}

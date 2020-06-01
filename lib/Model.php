<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Contracts\Resourceable;
use Beebmx\KirbyPay\Drivers\Driver;
use Beebmx\KirbyPay\Drivers\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;
use ArrayAccess;

abstract class Model implements Resourceable, JsonSerializable, ArrayAccess
{
    use ForwardsCalls;

    /**
     * Path of the Model
     *
     * @var string
     */
    protected static $path;

    /**
     * Extension of the Model
     *
     * @var string
     */
    protected static $type = '.json';

    /**
     * Attributes available for the instance Model
     *
     * @var array
     */
    public $attributes;

    /**
     * Create an instance of Model
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill(array_merge([
            'pay_id' => null,
            'uuid' => null
        ], $attributes));
        $this->boot();
    }

    /**
     * Setup extra options for Models
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Get an instance of current service driver
     *
     * @return Driver
     */
    protected static function driver()
    {
        return (new Factory)->find();
    }

    /**
     * Set the Model with attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Write attributes into a file
     *
     * @return Model
     */
    public function save()
    {
        return static::write($this->getAttributes(), $this->pay_id, $this->uuid);
    }

    /**
     * Delete the related file
     *
     * @return bool
     */
    public function delete(): bool
    {
        return !!static::destroy($this->pay_id, $this->uuid);
    }

    /**
     * Create a new instance with attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function newInstance($attributes = [])
    {
        return new static((array) $attributes);
    }

    /**
     * Get the attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get an attribute key
     *
     * @param $key
     * @return mixed|void
     */
    public function getAttribute($key)
    {
        if (!$key) {
            return;
        }

        return $this->attributes[$key];
    }

    /**
     * Set an attribute key
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if (!$key) {
            return;
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Determine if an attribute exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getAttribute($offset));
    }

    /**
     * Get an attribute like an array
     *
     * @param mixed $offset
     * @return mixed|void
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set an attribute like an array
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * Unset an attribute like an array
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Create an instance of resource
     *
     * @return \Beebmx\KirbyPay\Resource
     */
    public function newResource()
    {
        return new Resource(static::$type, static::$path, $this);
    }

    /**
     * Return all attributes as array
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Return all attributes like a json
     *
     * @return string
     */
    public function toJson()
    {
        return (new Collection(
            $this->toArray()
        ))->toJson();
    }

    /**
     * Return all attributes as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return (new Collection(
            $this->getAttributes()
        ))->toArray();
    }

    /**
     * Get an attribute
     *
     * @param $key
     * @return bool|mixed|void
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->getAttribute($key);
        }

        return false;
    }

    /**
     * Set an attribute
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Call statically a resource method
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->newResource(), $method, $parameters);
    }

    /**
     * Call statically a Model method
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    /**
     * Get a json with requires a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}

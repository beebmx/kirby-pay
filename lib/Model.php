<?php

namespace Beebmx\KirbyPay;

use Beebmx\KirbyPay\Contracts\Resourceable;
use Beebmx\KirbyPay\Drivers\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;
use ArrayAccess;

abstract class Model implements Resourceable, JsonSerializable, ArrayAccess
{
    use ForwardsCalls;

    protected static $path;

    protected static $type;

    public $attributes;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->boot();
    }

    public function boot()
    {
    }

    protected static function driver()
    {
        return (new Factory)->find();
    }

    public function fill(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function save()
    {
        return static::write($this->getAttributes(), $this->id, $this->uuid);
    }

    public function newInstance($attributes = [])
    {
        return new static((array) $attributes);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($key)
    {
        if (!$key) {
            return;
        }

        return $this->attributes[$key];
    }

    public function setAttribute($key, $value)
    {
        if (!$key) {
            return;
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    public function offsetExists($offset)
    {
        return ! is_null($this->getAttribute($offset));
    }

    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }


    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function newResource()
    {
        return new Resource(static::$type, static::$path, $this);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toJson()
    {
        return (new Collection(
            $this->toArray()
        ))->toJson();
    }

    public function toArray(): array
    {
        return (new Collection(
            $this->getAttributes()
        ))->toArray();
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->getAttribute($key);
        }

        return false;
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->newResource(), $method, $parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function __toString()
    {
        return $this->toJson();
    }
}

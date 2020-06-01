<?php

namespace Beebmx\KirbyPay;

class Log extends Model
{
    /**
     * Path of the Log Model
     *
     * @var string
     */
    protected static $path = 'logs';

    /**
     * Create a file and Log instance with given data
     *
     * @param array $data
     * @return Log
     */
    public static function create(array $data = [])
    {
        return static::write($data);
    }

    /**
     * Get the service log URL
     *
     * @return string
     */
    public static function serviceUrl(): string
    {
        return static::driver()->getUrls()['logs'];
    }
}

<?php


namespace Beebmx\KirbyPay;


class Log extends Model
{
    protected static $path = 'logs';

    protected static $type = '.json';

    public static function create(array $data = [])
    {
        return static::write($data);
    }
    public static function serviceUrl()
    {
        return static::driver()->getUrls()['logs'];
    }
}
<?php

namespace Beebmx\KirbyPay;

use Exception;
use Kirby\Toolkit\Dir;

class Storage
{
    /**
     * @param string $dir
     * @return bool|Exception
     */
    public static function create(string $dir = '')
    {
        if (!file_exists(static::getBase() . '/' . $dir)) {
            try {
                return Dir::make(static::getBase() . '/' . $dir);
            } catch (Exception $e) {
                return $e;
            }
        }
    }

    public static function path(string $dir = ''): string
    {
        return static::getBase() . '/' . $dir;
    }

    public static function files(string $dir = ''): array
    {
        return Dir::files(static::getBase() . '/' . $dir);
    }

    public static function isEmpty(string $dir = ''): bool
    {
        return Dir::isEmpty(static::getBase() . '/' . $dir);
    }

    /**
     * Rreturns the base path of the pay structure
     * @return string
     */
    protected static function getBase(): string
    {
        return is_string(option('beebmx.kirby-pay.storage'))
            ? option('beebmx.kirby-pay.storage')
            : option('beebmx.kirby-pay.storage')();
    }

    public static function count(string $dir = '')
    {
        return count(static::files($dir));
    }
}

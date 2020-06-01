<?php

namespace Beebmx\KirbyPay;

use Exception;
use Kirby\Toolkit\Dir;

class Storage
{
    /**
     * Creates the directory with the base path of the pay structure
     *
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

    /**
     * Get the path of the directory in the pay structure
     *
     * @param string $dir
     * @return string
     */
    public static function path(string $dir = ''): string
    {
        return static::getBase() . '/' . $dir;
    }

    /**
     * Get all the files in the directory structure
     *
     * @param string $dir
     * @return array
     */
    public static function files(string $dir = ''): array
    {
        return Dir::files(static::getBase() . '/' . $dir);
    }

    /**
     * Returns if the directory is empty
     *
     * @param string $dir
     * @return bool
     */
    public static function isEmpty(string $dir = ''): bool
    {
        return Dir::isEmpty(static::getBase() . '/' . $dir);
    }

    /**
     * Returns the base path of the pay structure
     *
     * @return string
     */
    protected static function getBase(): string
    {
        return is_string(option('beebmx.kirby-pay.storage'))
            ? option('beebmx.kirby-pay.storage')
            : option('beebmx.kirby-pay.storage')();
    }

    /**
     * Returns the number of files in the directory
     *
     * @param string $dir
     * @return int
     */
    public static function count(string $dir = ''): int
    {
        return count(static::files($dir));
    }
}

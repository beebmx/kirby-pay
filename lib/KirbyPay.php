<?php

namespace Beebmx\KirbyPay;

class KirbyPay
{
    const VERSION = '1.0.0';

    /**
     * Get the locale code
     *
     * @return string
     */
    public static function getLocaleCode()
    {
        return pay('locale_code', 'en');
    }
}

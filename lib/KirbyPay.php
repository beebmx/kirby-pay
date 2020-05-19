<?php

namespace Beebmx\KirbyPay;

class KirbyPay
{
    const VERSION = '1.0.0';

    public static function getLocaleCode()
    {
        return pay('locale_code', 'en');
    }
}

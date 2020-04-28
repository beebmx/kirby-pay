<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Contracts\Driverable;
use Exception;
use Kirby\Cms\App as Kirby;

abstract class Driver implements Driverable
{
    protected $service;

    protected $secret;

    protected $unit = 1;

    /**
     * Driver constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->service = $this->getServiceName();

        if (empty($this->secret) && empty(option("beebmx.kirby-pay.service_secret", null))) {
            throw new Exception("You must provide a secret key for the service {$this->service}");
        }

        if (!$this->secret) {
            $this->secret = option("beebmx.kirby-pay.service_secret", null);
        }

        $this->boot();
    }

    /**
     * Returns the current secret of the defined service
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    protected function getServiceName()
    {
        $class = array_slice(
            explode('\\', get_class($this)),
            -1,
            1
        )[0];

        return strtolower(substr($class, 0, -6));
    }

    protected function getLocale()
    {
        return option("beebmx.kirby-pay.locale", 'en');
    }

    public function preparePrice($amount): int
    {
        return $amount * $this->unit;
    }

    public function parsePrice($amount): float
    {
        return $amount / $this->unit;
    }
}

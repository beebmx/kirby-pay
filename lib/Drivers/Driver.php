<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Contracts\Driverable;

abstract class Driver implements Driverable
{
    protected $service;

    protected $secret;

    protected $unit = 1;

    /**
     * Driver constructor.
     * @throws DriverSecretException
     */
    public function __construct()
    {
        $this->service = $this->getServiceName();

        if (empty($this->secret) && empty(option('beebmx.kirby-pay.service_secret', null))) {
            throw new DriverSecretException("You must provide a secret key for the service {$this->service}");
        }

        if (!$this->secret) {
            $this->secret = option('beebmx.kirby-pay.service_secret', null);
        }

        $this->boot();
    }

    public function getPaymentMethods(): array
    {
        return $this->payment_methods;
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

    public function preparePrice($amount): int
    {
        return (float) $amount * $this->unit;
    }

    public function parsePrice($amount): float
    {
        return (int) $amount / $this->unit;
    }
}

<?php

namespace Beebmx\KirbyPay\Drivers;

use Beebmx\KirbyPay\Contracts\Driverable;
use Beebmx\KirbyPay\Exception\DriverSecretException;

abstract class Driver implements Driverable
{
    /**
     * Service name
     *
     * @var string
     */
    protected $service;

    /**
     * Secret key of the service
     *
     * @var string
     */
    protected $secret;

    /**
     * Unit of the service
     *
     * @var int
     */
    protected $unit = 1;

    /**
     * Create an instance of Driver
     *
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

    /**
     * Get the payment methods of the service
     *
     * @return array
     */
    public function getPaymentMethods(): array
    {
        return $this->payment_methods;
    }

    /**
     * Returns the current secret of the defined service
     *
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * Get the service name through class
     *
     * @return string
     */
    protected function getServiceName()
    {
        $class = array_slice(
            explode('\\', get_class($this)),
            -1,
            1
        )[0];

        return strtolower(substr($class, 0, -6));
    }

    /**
     * Parse the amount for the service
     *
     * @param $amount
     * @return int
     */
    public function preparePrice($amount): int
    {
        return (float) $amount * $this->unit;
    }

    /**
     * Get the real amount
     *
     * @param $amount
     * @return float
     */
    public function parsePrice($amount): float
    {
        return (int) $amount / $this->unit;
    }
}

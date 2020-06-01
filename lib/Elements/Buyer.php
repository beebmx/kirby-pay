<?php

namespace Beebmx\KirbyPay\Elements;

use Beebmx\KirbyPay\Contracts\Elementable;

class Buyer implements Elementable
{
    /**
     * Buyer name
     *
     * @var string
     */
    public $name;

    /**
     * Buyer email
     *
     * @var string
     */
    public $email;

    /**
     * Buyer phone
     *
     * @var string|null
     */
    public $phone;

    /**
     * Buyer id
     *
     * @var string|null
     */
    public $id;

    /**
     * Buyer customer_id
     *
     * @var string|null
     */
    public $customer_id;

    /**
     * Create an instance of Buyer
     *
     * @param string $name
     * @param string $email
     * @param string|null $phone
     * @param string|null $id
     * @param string|null $customer_id
     */
    public function __construct(string $name, string $email, string $phone = null, $id = null, $customer_id = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->id = $id;
        $this->customer_id = $customer_id ?? $id;
    }

    /**
     * Get the attributes of Buyer as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'id' => $this->id,
            'customer_id' => $this->customer_id,
        ];
    }
}

<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Routes;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
    /** @test */
    public function it_returns_all_routes_when_requested()
    {
        $this->assertIsArray(Routes::all());
        $this->assertNotNull(Routes::all());
    }

    /** @test */
    public function it_returns_a_method_name_from_a_name_key()
    {
        $this->assertIsArray(Routes::getMethodByName('customer.create'));
    }

    /** @test */
    public function it_returns_a_path_from_a_name_key()
    {
        $this->assertEquals(Routes::getBaseApiPath() . 'payments/customer', Routes::getRoutePathByName('customer.create'));
    }

    /** @test */
    public function it_returns_the_path_from_name_key_but_with_helper()
    {
        $this->assertEquals(Routes::getBaseApiPath() . 'payments/customer', kpUrl('customer.create'));
    }

    /** @test */
    public function it_returns_a_method_from_a_name_key()
    {
        $this->assertEquals('post', Routes::getRouteMethodByName('customer.create'));
    }

    /** @test */
    public function it_returns_the_method_from_name_key_but_with_helper()
    {
        $this->assertEquals('post', kpMethod('customer.create'));
    }
}

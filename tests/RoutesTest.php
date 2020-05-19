<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Routes\ApiRoutes;
use Beebmx\KirbyPay\Routes\Routes;

class RoutesTest extends TestCase
{
    /** @test */
    public function it_returns_an_array_of_all_routes()
    {
        $this->assertIsArray(Routes::all());
        $this->assertNotNull(Routes::all());
    }

    /** @test */
    public function it_returns_an_array_of_all_api_routes()
    {
        $this->assertIsArray(ApiRoutes::all());
        $this->assertNotNull(ApiRoutes::all());
    }

    /** @test */
    public function it_returns_a_method_name_from_a_name_key()
    {
        $this->assertIsArray(Routes::getMethodByName('payment.create'));
    }

    /** @test */
    public function it_returns_a_path_from_a_name_key()
    {
        $this->assertEquals(Routes::getBaseApiPath() . 'payment/create', Routes::getRoutePathByName('payment.create'));
    }

    /** @test */
    public function it_returns_the_path_from_name_key_but_with_helper()
    {
        $this->assertEquals(Routes::getBaseApiPath() . 'payment/create', kpUrl('payment.create'));
    }

    /** @test */
    public function it_returns_a_method_from_a_name_key()
    {
        $this->assertEquals('post', Routes::getRouteMethodByName('payment.create'));
    }

    /** @test */
    public function it_returns_the_method_from_name_key_but_with_helper()
    {
        $this->assertEquals('post', kpMethod('payment.create'));
    }
}

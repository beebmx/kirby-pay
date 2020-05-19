<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Log;
use Kirby\Cms\App;
use Kirby\Toolkit\Dir;

class LogTest extends TestCase
{
    public function setUp(): void
    {
        new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => __DIR__ . '/tmp',
            ]
        ]);
    }

    public function tearDown(): void
    {
        Dir::remove(__DIR__ . '/tmp/logs');
    }

    /** @test */
    public function a_log_creates_a_record_in_logs_directory()
    {
        $this->assertTrue(Log::isEmpty());
        Log::create(['foo' => 'bar']);
        Log::create(['bar' => 'baz']);

        $this->assertFalse(Log::isEmpty());
        $this->assertEquals(2, Log::count());
        $this->assertCount(2, Log::get());
    }

    /** @test */
    public function a_log_can_read_the_data()
    {
        Log::create(['foo' => 'bar']);
        $this->assertEquals('bar', Log::first()->foo);
    }
}

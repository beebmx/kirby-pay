<?php

namespace Beebmx\KirbyPay\Tests;

use Beebmx\KirbyPay\Storage;
use Kirby\Cms\App;
use Kirby\Toolkit\Dir;

class StorageTest extends TestCase
{
    public $kirby;

    public $dir;

    public function setUp(): void
    {
        $this->dir = __DIR__ . '/tmp';
        $this->kirby = new App([
            'roots' => [
                'index' => '/dev/null',
            ],
            'options' => [
                'beebmx.kirby-pay.storage' => $this->dir,
            ]
        ]);
    }

    public function tearDown(): void
    {
        Dir::remove($this->dir . '/demo');
    }

    /** @test */
    public function it_creates_a_directory_only_once()
    {
        $this->assertDirectoryNotExists($this->dir . '/demo');
        Storage::create('demo');

        $this->assertDirectoryExists($this->dir . '/demo');
        Storage::create('demo');
        $this->assertDirectoryExists($this->dir . '/demo');
    }

    /** @test */
    public function it_validates_than_a_directory_is_empty()
    {
        Storage::create('demo');
        $this->assertTrue(Storage::isEmpty('demo'));
        touch($this->dir . '/demo' . '/file');

        $this->assertFalse(Storage::isEmpty('demo'));
    }

    /** @test */
    public function it_counts_all_the_files_in_directory()
    {
        Storage::create('demo');
        $this->assertEquals(0, Storage::count('demo'));

        touch($this->dir . '/demo' . '/file-01');
        touch($this->dir . '/demo' . '/file-02');
        touch($this->dir . '/demo' . '/file-03');
        $this->assertEquals(3, Storage::count('demo'));
    }

    /** @test */
    public function it_returns_the_full_path_of_given_directory()
    {
        Storage::create('demo');
        $this->assertEquals($this->dir . '/demo', Storage::path('demo'));
    }

    /** @test */
    public function it_returns_all_the_files_in_given_directory()
    {
        Storage::create('demo');

        $this->assertIsArray(Storage::files('demo'));
        $this->assertEquals([], Storage::files('demo'));

        touch($this->dir . '/demo' . '/file-01.json');
        touch($this->dir . '/demo' . '/file-02.json');
        touch($this->dir . '/demo' . '/file-03.json');

        $this->assertEquals([
            'file-01.json',
            'file-02.json',
            'file-03.json',
        ], Storage::files('demo'));
    }
}

<?php

namespace App\Tests\Services\ChambersService;

use App\Services\ChambersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetTest extends KernelTestCase
{
    private $container;
    private $chamberService;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->chamberService = $this->container->get(ChambersService::class);
    }

    public function testGet(): void
    {
        // сначала создаем а потом получаем id
        $chamber = $this->chamberService->get(1);

        $this->assertArrayHasKey('type',$chamber);
        $this->assertArrayHasKey('code',$chamber);
        $this->assertArrayHasKey('message',$chamber);
        $this->assertArrayHasKey('data',$chamber);
    }
}
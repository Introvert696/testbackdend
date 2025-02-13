<?php

namespace App\Tests\Services\ChambersService;

use App\Services\ChambersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AllTest extends KernelTestCase
{
    private $container;
    private $chamberService;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->chamberService = $this->container->get(ChambersService::class);
    }

    public function testAll(): void
    {
        $chambers = $this->chamberService->all();

        $this->assertArrayHasKey('type',$chambers);
        $this->assertArrayHasKey('code',$chambers);
        $this->assertArrayHasKey('message',$chambers);
        $this->assertArrayHasKey('data',$chambers);
    }
}
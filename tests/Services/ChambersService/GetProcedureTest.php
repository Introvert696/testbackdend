<?php

namespace App\Tests\Services\ChambersService;

use App\Services\ChambersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetProcedureTest extends KernelTestCase
{
    private $container;
    private $chamberService;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->chamberService = $this->container->get(ChambersService::class);
    }

    public function testGetProcedure(): void
    {
        $procedures = $this->chamberService->getProcedure(1);

        $this->assertArrayHasKey('type',$procedures);
        $this->assertArrayHasKey('code',$procedures);
        $this->assertArrayHasKey('message',$procedures);
        $this->assertArrayHasKey('data',$procedures);
    }
}
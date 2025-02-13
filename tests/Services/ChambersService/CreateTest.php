<?php

namespace App\Tests\Services\ChambersService;

use App\Services\ChambersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateTest extends KernelTestCase
{
    private $container;
    private $chamberService;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->chamberService = $this->container->get(ChambersService::class);
    }

    public function testCreate(): void
    {
        $data = [
            "number" => 5434
        ];
        $data = json_encode($data);
        $res = $this->chamberService->create($data);

        $this->assertArrayHasKey('type',$res);
        $this->assertArrayHasKey('code',$res);
        $this->assertArrayHasKey('message',$res);
        $this->assertArrayHasKey('data',$res);

    }
}
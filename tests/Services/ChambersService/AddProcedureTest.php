<?php

namespace App\Tests\Services\ChambersService;

use App\Services\ChambersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddProcedureTest extends KernelTestCase
{
    private $container;
    private $chamberService;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->chamberService = $this->container->get(ChambersService::class);
    }

    public function testAddProcedure(): void
    {
        $data =[
            [
                "procedure_id"=> 2,
                "queue"=> 3,
                "status"=> true
            ],
            [
                "procedure_id"=> 1,
                "queue"=> 3,
                "status"=> true
            ],
        ];
        $data = json_encode($data);
        $res = $this->chamberService->addProcedure(1,$data);


        $this->assertArrayHasKey('type',$res);
        $this->assertArrayHasKey('code',$res);
        $this->assertArrayHasKey('message',$res);
        $this->assertArrayHasKey('data',$res);
    }
}
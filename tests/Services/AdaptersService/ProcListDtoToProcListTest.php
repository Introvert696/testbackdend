<?php

namespace App\Tests\Services\AdaptersService;

use App\DTO\ProcListDTO;
use App\Services\AdaptersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProcListDtoToProcListTest extends KernelTestCase
{
    private $container;
    private $adapterService;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->adapterService = $this->container->get(AdaptersService::class);
    }
    public function testMain(): void
    {
        $procListDto = new ProcListDTO();
        $procListDto->setSourceType('test');
        $procListDto->setSourceId(2);
        $procListDto->setQueue(5);
        $procListDto->setStatus(false);
        $procListDto->setProcedureId(9999);
        $procListDto->setProclistId(4);

        $procList = $this->adapterService->procListDtoToProcList($procListDto,1);

        $this->assertObjectHasProperty('id',$procList);
        $this->assertObjectHasProperty('procedures',$procList);
        $this->assertObjectHasProperty('queue',$procList);
        $this->assertObjectHasProperty('source_id',$procList);
        $this->assertObjectHasProperty('source_type',$procList);
        $this->assertObjectHasProperty('status',$procList);

    }
}
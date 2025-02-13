<?php
namespace App\Tests\Services\AdaptersService;

use App\Entity\ProcedureList;
use App\Services\AdaptersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProcListToProcListRespDTOTest extends KernelTestCase
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
        $pl = new ProcedureList();
        $pl->setSourceType('chamber');
        $pl->setSourceId(2);
        $pl->setQueue(1);
        $pl->setStatus(false);

        $procRespDTO = $this->adapterService->procListToProcListRespDTO($pl);

        $this->assertObjectHasProperty('queue',$procRespDTO);
        $this->assertObjectHasProperty('source_id',$procRespDTO);
        $this->assertObjectHasProperty('source_type',$procRespDTO);
        $this->assertObjectHasProperty('status',$procRespDTO);
    }
}
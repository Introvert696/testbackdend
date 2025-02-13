<?php

namespace App\Tests\Services\AdaptersService;

use App\Entity\Procedures;
use App\Services\AdaptersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProcedureToProcedureResponseDTOTest extends KernelTestCase
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
        $procedure = new Procedures();
        $procedure->setTitle("tst");
        $procedure->setDescription("Tests ");
        $procRespDTO = $this->adapterService->procedureToProcedureResponseDTO($procedure);

        $this->assertObjectHasProperty('id',$procRespDTO);
        $this->assertObjectHasProperty('title',$procRespDTO);
        $this->assertObjectHasProperty('description',$procRespDTO);
        $this->assertObjectHasProperty('entityList',$procRespDTO);
    }
}
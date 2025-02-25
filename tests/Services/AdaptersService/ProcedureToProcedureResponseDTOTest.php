<?php

namespace App\Tests\Services\AdaptersService;

use App\Entity\Procedures;
use App\Tests\Services\BaseService;

class ProcedureToProcedureResponseDTOTest extends BaseService
{
    public function testValid(): void
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
    public function testNotValid(): void
    {
        $procedure = new Procedures();
        $procRespDTO = $this->adapterService->procedureToProcedureResponseDTO($procedure);

        $this->assertFalse($procRespDTO);
    }
}
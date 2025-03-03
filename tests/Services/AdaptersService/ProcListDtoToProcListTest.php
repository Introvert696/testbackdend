<?php

namespace App\Tests\Services\AdaptersService;

use App\DTO\Chamber\ProcedureListDTO;
use App\Tests\Services\BaseService;

class ProcListDtoToProcListTest extends BaseService
{
    public function testValid(): void
    {
        $procListDto = new ProcedureListDTO();
        $procListDto->setSourceType('test');
        $procListDto->setSourceId(2);
        $procListDto->setQueue(5);
        $procListDto->setStatus(false);
        $procListDto->setProcedureId(9999);
        $procListDto->setProclistId(4);

        $procList = $this->adapterService->convertProcedureListDtoToProcedureList($procListDto, 1);

        $this->assertObjectHasProperty('id', $procList);
        $this->assertObjectHasProperty('procedures', $procList);
        $this->assertObjectHasProperty('queue', $procList);
        $this->assertObjectHasProperty('source_id', $procList);
        $this->assertObjectHasProperty('source_type', $procList);
        $this->assertObjectHasProperty('status', $procList);
    }

    public function testNotValid(): void
    {
        $procListDto = new ProcedureListDTO();
        $procList = $this->adapterService->convertProcedureListDtoToProcedureList($procListDto, 1);
        $this->assertFalse($procList);
    }
}
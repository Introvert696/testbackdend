<?php

namespace App\Tests\Services\ValidateService;

use App\DTO\Chamber\ProcedureListDTO;
use App\Tests\Services\BaseService;

class ProcedureListWithProcedureTest extends BaseService
{
    public function testNotValid(): void
    {
        $procListDTO = new ProcedureListDTO();

        $result = $this->validateService->validateProcedureListWithProcedure($procListDTO);
        $this->assertFalse($result);
    }

    public function testValid(): void
    {
        $procListDTO = new ProcedureListDTO();
        $procListDTO->setProcedureId(5);
        $procListDTO->setQueue(3);
        $procListDTO->setStatus(true);
        $procListDTO->setSourceType('chambers');
        $procListDTO->setSourceId(5);
        $procListDTO->setProclistId(6);

        $result = $this->validateService->validateProcedureListWithProcedure($procListDTO);
        $this->assertNotNull($result);
    }
}
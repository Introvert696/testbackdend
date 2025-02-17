<?php
namespace App\Tests\Services\ValidateService;

use App\DTO\ProcListDTO;
use App\Tests\Services\BaseService;

class ProcedureListWithProcedureTest extends BaseService
{
    public function testNotValid(): void
    {
        $procListDTO = new ProcListDTO();
        $result = $this->validateService->procedureListWithProcedure($procListDTO);
        $this->assertNull($result);
    }
    public function testValid(): void
    {
        $procListDTO = new ProcListDTO();
        $procListDTO->setProcedureId(5);
        $procListDTO->setQueue(3);
        $procListDTO->setStatus(true);
        $procListDTO->setSourceType('chambers');
        $procListDTO->setSourceId(5);
        $procListDTO->setProclistId(6);

        $result = $this->validateService->procedureListWithProcedure($procListDTO);
        $this->assertNotNull($result);
    }
}
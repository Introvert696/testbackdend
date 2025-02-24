<?php

namespace App\Tests\Services\ValidateService;

use App\DTO\Chamber\ProcListDTO;
use App\Tests\Services\BaseService;

class ProcListDTOTest extends BaseService
{
    public function testValid(): void
    {
        $procListDto = new ProcListDTO();
        $procListDto->setQueue(2);
        $procListDto->setStatus(true);
        $procListDto->setSourceId(3);
        $procListDto->setSourceType("chamber");
        $procListDto->setProclistId(4);
        $procListDto->setProcedureId(2);
        $response = $this->validateService->procListDTO($procListDto);
        $this->assertNotNull($response);
        $this->assertObjectHasProperty('queue',$response);
    }
    public function testNotValid(): void
    {
        $procListDto = new ProcListDTO();
        $response = $this->validateService->procListDTO($procListDto);
        $this->assertNull($response);
    }
}
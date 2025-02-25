<?php
namespace App\Tests\Services\AdaptersService;

use App\Entity\ProcedureList;
use App\Tests\Services\BaseService;

class ProcListToProcListRespDTOTest extends BaseService
{
    public function testValid(): void
    {
        $pl = $this->procedureListRepository->find(1);

        $procRespDTO = $this->adapterService->procListToProcListRespDTO($pl);

        $this->assertObjectHasProperty('queue',$procRespDTO);
        $this->assertObjectHasProperty('source_id',$procRespDTO);
        $this->assertObjectHasProperty('source_type',$procRespDTO);
        $this->assertObjectHasProperty('status',$procRespDTO);
    }
    public function testNotValid(): void
    {
        $pl = new ProcedureList();
        $procRespDTO = $this->adapterService->procListToProcListRespDTO($pl);
        $this->assertFalse($procRespDTO);
    }
}
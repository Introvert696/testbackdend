<?php
namespace App\Tests\Services\AdaptersService;

use App\Entity\ProcedureList;
use App\Tests\Services\BaseService;

class ProcListToProcListRespDTOTest extends BaseService
{
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
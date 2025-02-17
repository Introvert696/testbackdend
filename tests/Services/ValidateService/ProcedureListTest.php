<?php
namespace App\Tests\Services\ValidateService;

use App\Entity\ProcedureList;
use App\Entity\Procedures;
use App\Tests\Services\BaseService;

class ProcedureListTest extends BaseService
{
    public function testNotValid(): void
    {
        $procedureList = new ProcedureList();
        $response = $this->validateService->procedureList($procedureList);
        $this->assertNull($response);
    }
    public function testValid(): void
    {
        $procList = $this->procedureListRepository->find(2);
        $response = $this->validateService->procedureList($procList);
        $this->assertNotNull($response);
    }
}
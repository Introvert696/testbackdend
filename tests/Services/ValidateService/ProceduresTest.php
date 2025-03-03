<?php

namespace App\Tests\Services\ValidateService;

use App\Entity\Procedures;
use App\Tests\Services\BaseService;

class ProceduresTest extends BaseService
{
    public function testNotValid(): void
    {
        $procedure = new Procedures();
        $response = $this->validateService->validateProcedures($procedure);
        $this->assertFalse($response);
    }

    public function testValid(): void
    {
        $procedure = new Procedures();
        $procedure->setTitle("dddd");
        $procedure->setDescription("sfsfd");
        $response = $this->validateService->validateProcedures($procedure);
        $this->assertNotNull($response);
    }
}
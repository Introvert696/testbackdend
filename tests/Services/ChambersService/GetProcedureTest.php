<?php

namespace App\Tests\Services\ChambersService;

use App\Tests\Services\BaseService;

class GetProcedureTest extends BaseService
{
    public function testGetProcedure(): void
    {
        $procedures = $this->chamberService->getProcedure(1);

        $this->assertArrayHasKey('type',$procedures);
        $this->assertArrayHasKey('code',$procedures);
        $this->assertArrayHasKey('message',$procedures);
        $this->assertArrayHasKey('data',$procedures);
    }
}
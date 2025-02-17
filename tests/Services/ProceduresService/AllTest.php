<?php

namespace App\Tests\Services\ProceduresService;

use App\Tests\Services\BaseService;

class AllTest extends BaseService
{
    public function testMain(): void
    {
        $procedures = $this->procedureService->all();

        $this->assertArrayHasKey('type',$procedures);
        $this->assertArrayHasKey('message',$procedures);
        $this->assertArrayHasKey('code',$procedures);
        $this->assertArrayHasKey('data',$procedures);

    }
}
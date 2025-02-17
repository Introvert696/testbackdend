<?php

namespace App\Tests\Services\ProceduresService;

use App\Tests\Services\BaseService;

class AboutTest extends BaseService
{
    public function testMain(): void
    {
        $about = $this->procedureService->about(1);

        $this->assertArrayHasKey('type',$about);
        $this->assertArrayHasKey('message',$about);
        $this->assertArrayHasKey('code',$about);
        $this->assertArrayHasKey('data',$about);
    }
}
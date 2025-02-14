<?php

namespace App\Tests\Services\ChambersService;

use App\Tests\Services\BaseService;

class AllTest extends BaseService
{
    public function testAll(): void
    {
        $chambers = $this->chamberService->all();

        $this->assertArrayHasKey('type',$chambers);
        $this->assertArrayHasKey('code',$chambers);
        $this->assertArrayHasKey('message',$chambers);
        $this->assertArrayHasKey('data',$chambers);
    }
}
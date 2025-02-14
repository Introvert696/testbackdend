<?php

namespace App\Tests\Services\ChambersService;

use App\Tests\Services\BaseService;

class DeleteTest extends BaseService
{
    public function testMain(): void
    {
        $chamber = $this->chamberService->delete(99);

        $this->assertArrayHasKey('type',$chamber);
        $this->assertArrayHasKey('code',$chamber);
        $this->assertArrayHasKey('message',$chamber);
    }
}
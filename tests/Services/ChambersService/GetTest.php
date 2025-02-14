<?php

namespace App\Tests\Services\ChambersService;

use App\Tests\Services\BaseService;

class GetTest extends BaseService
{
    public function testGet(): void
    {
        // сначала создаем а потом получаем id
        $chamber = $this->chamberService->get(1);

        $this->assertArrayHasKey('type',$chamber);
        $this->assertArrayHasKey('code',$chamber);
        $this->assertArrayHasKey('message',$chamber);
        $this->assertArrayHasKey('data',$chamber);
    }
}
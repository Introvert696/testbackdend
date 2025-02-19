<?php

namespace App\Tests\Services\ValidateService;

use App\Tests\Services\BaseService;

class ValidateTest extends BaseService
{
    public function testMain(): void
    {
        $result = $this->validateService->validate(null,["df"=>"dfd"]);
       $this->assertNull($result[0]);
       $this->assertNotNull($result[1]);
    }
}
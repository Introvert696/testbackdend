<?php

namespace App\Tests\Services\ValidateService;

use App\Entity\Chambers;
use App\Tests\Services\BaseService;

class ChambersRequestDataTest extends BaseService
{
    public function testNotValid(): void
    {
        $data = new Chambers();
        $response = $this->validateService->chambersRequestData($data);
        $this->assertNull($response);
    }
    public function testValid(): void
    {
        $data = new Chambers();
        $data->setNumber(12344);
        $response = $this->validateService->chambersRequestData($data);
        $this->assertNotNull($response);
    }
}
<?php

namespace App\Tests\Services\PatientsServices;

use App\Tests\Services\BaseService;

class RemoveTest extends BaseService
{
    public function testMain(): void
    {
        $response = $this->patientsServices->remove(1);
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
    }
}
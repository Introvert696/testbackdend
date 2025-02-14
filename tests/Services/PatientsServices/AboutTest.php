<?php

namespace App\Tests\Services\PatientsServices;

use App\Tests\Services\BaseService;

class AboutTest extends BaseService
{
    public function testMain(): void
    {
        $response = $this->patientsServices->about(1);
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
//        $this->assertArrayHasKey('data',$response);
    }
}
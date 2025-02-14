<?php
namespace App\Tests\Services\PatientsServices;

use App\Tests\Services\BaseService;

class CreateOrFindTest extends BaseService
{
    public function testMain(): void
    {
        $response = $this->patientsServices->all();
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertArrayHasKey('data',$response);
    }
}
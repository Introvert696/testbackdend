<?php

namespace App\Tests\Services\PatientsServices;

use App\Tests\Services\BaseService;

class DeleteTest extends BaseService
{
    public function testValid(): void
    {
        $patient = [
            "name" => "test user",
            "card_number" => 2
        ];
        $response= $this->patientsServices->createOrFind(json_encode($patient));
        $patient = $response['data'];

        $response = $this->patientsServices->delete($patient->getId());
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertSame('Ok',$response['type']);
        $this->assertSame(200,$response['code']);
    }
    public function testNotValidPatient(): void
    {
        $response = $this->patientsServices->delete(0);
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertSame('Not found',$response['type']);
        $this->assertSame(404,$response['code']);
    }
}
<?php
namespace App\Tests\Services\PatientsServices;

use App\Tests\Services\BaseService;

class StoreTest extends BaseService
{
    public function testEmptyBody(): void
    {
        $data = [];
        $response = $this->patientsServices->store(json_encode($data));
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertSame('Error',$response['type']);
        $this->assertSame(502,$response['code']);
    }
    public function testInvalidBody(): void
    {
        $data = 'sdfsdf';
        $response = $this->patientsServices->store($data);
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertSame('Error',$response['type']);
        $this->assertSame(502,$response['code']);
    }
    public function testValidBody(): void
    {
        $data = [
            "card_number" => 1123,
            "name" => "Test from unit test"
        ];
        $response = $this->patientsServices->store(json_encode($data));
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertArrayHasKey('data',$response);
        $this->assertSame('Created',$response['type']);
        $this->assertSame(200,$response['code']);
        if($response['data']){
            $this->patientsServices->delete($response['data']?->getId());
        }
    }
}
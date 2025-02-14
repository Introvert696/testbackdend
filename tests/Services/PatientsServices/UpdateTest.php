<?php
namespace App\Tests\Services\PatientsServices;

use App\Tests\Services\BaseService;

class UpdateTest extends BaseService
{
    public function testMain(): void
    {
        $id= 14;
        $data = [
            "name"=>"test test test",
            "chamber" => 2
        ];
        $data = json_encode($data);
        $response = $this->patientsServices->update($id,$data);

        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
//        $this->assertArrayHasKey('data',$response);

        $this->assertSame(200,$response['code']);

    }
}
<?php

namespace App\Tests\Services\ProceduresService;

use App\Tests\Services\BaseService;

class StoreTest extends BaseService
{
    public function testInValidData(): void
    {
        // test Data
        $data = [
            "main" => "dfdf"
        ];
        $response = $this->procedureService->store(json_encode($data));
        $this->assertSame($response['code'],422);
    }
    public function testValidData(): void
    {
        $data =[
            "title"=>"test tsa",
            "description" => "dddsdsd"
        ];
        $response = $this->procedureService->store(json_encode($data));
        $this->assertSame($response['code'],200);
        $this->em->remove($response['data']);
        $this->em->flush();

    }
}
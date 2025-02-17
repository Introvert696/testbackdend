<?php

namespace App\Tests\Services\ProceduresService;

use App\Tests\Services\BaseService;

class UpdateTest extends BaseService
{
    public function testNotValidData(): void
    {
        $id = 1;
        $data = [];
        $response = $this->procedureService->update($id,json_encode($data));
        $this->assertSame($response['code'],422);
        $this->assertSame($response['type'],'Error');
    }
    public function testValidData(): void
    {
        $id = 1;
        $data = [
            "title"=>"t3le",
            "description" => "function d"
        ];
        $response = $this->procedureService->update($id,json_encode($data));
        $this->assertSame($response['code'],200);
        $this->assertSame($response['type'],'Update');
    }
}
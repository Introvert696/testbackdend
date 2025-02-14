<?php

namespace App\Tests\Services\ChambersService;

use App\Tests\Services\BaseService;

class AddProcedureTest extends BaseService
{

    public function testAddProcedure(): void
    {
        $data =[
            [
                "procedure_id"=> 2,
                "queue"=> 3,
                "status"=> true
            ],
            [
                "procedure_id"=> 1,
                "queue"=> 3,
                "status"=> true
            ],
        ];
        $data = json_encode($data);
        $res = $this->chamberService->addProcedure(1,$data);


        $this->assertArrayHasKey('type',$res);
        $this->assertArrayHasKey('code',$res);
        $this->assertArrayHasKey('message',$res);
        $this->assertArrayHasKey('data',$res);
    }
}
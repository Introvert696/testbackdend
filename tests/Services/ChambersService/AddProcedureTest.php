<?php

namespace App\Tests\Services\ChambersService;

use App\Tests\Services\BaseService;

class AddProcedureTest extends BaseService
{

    public function testValid(): void
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

        $this->assertSame(200,$res['code']);
        $this->assertArrayHasKey('type',$res);
        $this->assertArrayHasKey('code',$res);
        $this->assertArrayHasKey('message',$res);
        $this->assertArrayHasKey('data',$res);
    }
    public function testNotValidId(): void
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
        $res = $this->chamberService->addProcedure(999999,$data);

        $this->assertSame(404,$res['code']);
        $this->assertArrayHasKey('type',$res);
        $this->assertArrayHasKey('code',$res);
        $this->assertArrayHasKey('message',$res);
    }
    public function testEmptyData(): void
    {
        $data =[];
        $data = json_encode($data);
        $res = $this->chamberService->addProcedure(1,$data);
        $this->assertSame(422,$res['code']);
        $this->assertArrayHasKey('type',$res);
        $this->assertArrayHasKey('code',$res);
        $this->assertArrayHasKey('message',$res);
    }
    public function testNotValidJson(): void
    {
        $data = "['asdfas'asdfas ]asdf";
        $res = $this->chamberService->addProcedure(1,$data);
        $this->assertSame(422,$res['code']);
        $this->assertArrayHasKey('type',$res);
        $this->assertArrayHasKey('code',$res);
        $this->assertArrayHasKey('message',$res);
    }
}
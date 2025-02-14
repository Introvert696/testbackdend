<?php

namespace App\Tests\Services\ChambersService;

use App\Tests\Services\BaseService;

class UpdateTest extends BaseService
{
    public function testConflict() : void
    {
        $id = 1;
//        conflict number
        $data = [
            "number"=>313
        ];
        $response = $this->chamberService->update($id,json_encode($data));
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertSame($response['type'],"Conflict");
    }

}
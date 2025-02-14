<?php

namespace App\Tests\Services\ChambersService;

use App\Tests\Services\BaseService;

class CreateTest extends BaseService
{
    public function testCreate(): void
    {
        $data = [
            "number" => 5434
        ];
        $data = json_encode($data);
        $res = $this->chamberService->create($data);

        $this->assertArrayHasKey('type',$res);
        $this->assertArrayHasKey('code',$res);
        $this->assertArrayHasKey('message',$res);
        $this->assertArrayHasKey('data',$res);

    }
}
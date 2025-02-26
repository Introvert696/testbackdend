<?php

namespace App\Tests\Services\ResponseHelper;

use App\Tests\Services\BaseService;

class CheckRequestTest extends BaseService
{
    public function testValidData(): void
    {
        $classname = 'App\Entity\Chambers';
        $data = [
            "number"=> 228
        ];
        $response =  $this->jsonResopnseHelper->checkRequest(json_encode($data),$classname);

        $this->assertIsObject($response);
        $this->assertSame($classname,$response::class);
    }
    public function testEmptyData(): void
    {
        $classname = 'App\Entity\Chambers';
        $data = [
        ];
        $response =  $this->jsonResopnseHelper->checkRequest(json_encode($data),$classname);

        $this->assertIsObject($response);
        $this->assertSame($classname,$response::class);
    }
    public function testNotValidData(): void
    {
        $classname = 'App\Entity\Chambers';
        $data = "asdfafasfasdf";
        $response =  $this->jsonResopnseHelper->checkRequest($data,$classname);

        $this->assertFalse($response);
    }
    public function testNotValidClass(): void
    {
        $classname = 'asfas';
        $data = [
            "number"=> 228
        ];
        $response =  $this->jsonResopnseHelper->checkRequest(json_encode($data),$classname);
        $this->assertFalse($response);
    }

}
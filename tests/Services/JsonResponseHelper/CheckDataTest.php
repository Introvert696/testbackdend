<?php

namespace App\Tests\Services\JsonResponseHelper;

use App\Tests\Services\BaseService;

class CheckDataTest extends BaseService
{
    public function testValidData(): void
    {
        $classname = 'App\Entity\Chambers';
        $data = [
            "number"=> 228
        ];
        $response =  $this->jsonResopnseHelper->checkData(json_encode($data),$classname);

        $this->assertIsObject($response);
        $this->assertSame($classname,$response::class);
    }
    public function testEmptyData(): void
    {
        $classname = 'App\Entity\Chambers';
        $data = [
        ];
        $response =  $this->jsonResopnseHelper->checkData(json_encode($data),$classname);

        $this->assertIsObject($response);
        $this->assertSame($classname,$response::class);
    }
    public function testNotValidData(): void
    {
        $classname = 'App\Entity\Chambers';
        $data = "asdfafasfasdf";
        $response =  $this->jsonResopnseHelper->checkData($data,$classname);

        $this->assertNull($response);
    }
    public function testNotValidClass(): void
    {
        $classname = 'asfas';
        $data = [
            "number"=> 228
        ];
        $response =  $this->jsonResopnseHelper->checkData(json_encode($data),$classname);
        $this->assertNull($response);
    }

}
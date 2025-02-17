<?php
namespace App\Tests\Services\JsonResponseHelper;

use App\Tests\Services\BaseService;

class GenerateTest extends BaseService
{
    public function testAllData(): void
    {
        $response = $this->jsonResopnseHelper->generate('Ok',200,'message',["test"=>"test"]);

        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertArrayHasKey('data',$response);

        $this->assertSame('Ok',$response['type']);
        $this->assertSame(200,$response['code']);
        $this->assertSame('message',$response['message']);
        $this->assertSame(["test"=>"test"],$response['data']);
    }
    public function testWithoutData(): void
    {
        $response = $this->jsonResopnseHelper->generate('Ok',200,'message');

        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertArrayNotHasKey('data',$response);

        $this->assertSame('Ok',$response['type']);
        $this->assertSame(200,$response['code']);
    }
}
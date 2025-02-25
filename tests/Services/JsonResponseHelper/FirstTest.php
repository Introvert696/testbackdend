<?php
namespace App\Tests\Services\JsonResponseHelper;

use App\Entity\Chambers;
use App\Tests\Services\BaseService;

class FirstTest extends BaseService
{
    public function testMain(): void
    {
        $data = [
            new Chambers(),
            new Chambers()
        ];
        $response = $this->jsonResopnseHelper->first($data);
        $this->assertSame('App\Entity\Chambers',$response::class);
    }
    public function testEmptyArray(): void
    {
        $data = [ ];

        $response = $this->jsonResopnseHelper->first($data);
        $this->assertFalse($response);
    }

}
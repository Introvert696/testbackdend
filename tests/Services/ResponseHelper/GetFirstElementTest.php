<?php

namespace App\Tests\Services\ResponseHelper;

use App\Entity\Chambers;
use App\Tests\Services\BaseService;

class GetFirstElementTest extends BaseService
{
    public function testMain(): void
    {
        $data = [
            new Chambers(),
            new Chambers()
        ];
        $response = $this->jsonResopnseHelper->getFirstElement($data);
        $this->assertSame('App\Entity\Chambers', $response::class);
    }

    public function testEmptyArray(): void
    {
        $data = [];

        $response = $this->jsonResopnseHelper->getFirstElement($data);
        $this->assertFalse($response);
    }

}
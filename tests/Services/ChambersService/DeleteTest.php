<?php

namespace App\Tests\Services\ChambersService;

use App\Entity\Chambers;
use App\Tests\Services\BaseService;

class DeleteTest extends BaseService
{
    public function testExistsChamber(): void
    {
        $chamber = new Chambers();
        $chamber->setNumber(3);
        $this->em->persist($chamber);
        $this->em->flush();

        $response = $this->chamberService->delete($chamber->getId());

        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);

        $this->assertSame($response['type'],'Delete');
        $this->assertSame($response['code'],202);
    }
    public function testNotFound(): void
    {
        $response = $this->chamberService->delete(3000);

        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);

        $this->assertSame('Not found',$response['type']);
        $this->assertSame(404,$response['code']);
    }
}
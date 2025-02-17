<?php

namespace App\Tests\Services\ChambersService;

use App\Entity\Chambers;
use App\Tests\Services\BaseService;

class GetTest extends BaseService
{
    public function testValidData(): void
    {
        $chamber = new Chambers();
        $chamber->setNumber(8765);
        $this->em->persist($chamber);
        $this->em->flush();
        $chamberService = $this->chamberService->get($chamber->getId());

        $this->assertArrayHasKey('type',$chamberService);
        $this->assertArrayHasKey('code',$chamberService);
        $this->assertArrayHasKey('message',$chamberService);
        $this->assertArrayHasKey('data',$chamberService);
        $this->em->remove($chamber);
        $this->em->flush();
    }
    public function testNotValid(): void
    {
        $chamber = $this->chamberService->get(753456);
        $this->assertArrayHasKey('type',$chamber);
        $this->assertArrayHasKey('code',$chamber);
        $this->assertArrayHasKey('message',$chamber);
    }
}
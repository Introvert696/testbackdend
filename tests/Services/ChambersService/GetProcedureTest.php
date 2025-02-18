<?php

namespace App\Tests\Services\ChambersService;

use App\Entity\Chambers;
use App\Tests\Services\BaseService;

class GetProcedureTest extends BaseService
{
    public function testWithoutProcedure(): void
    {
        $chamber = new Chambers();
        $chamber->setNumber(1332);
        $this->em->persist($chamber);
        $this->em->flush();
        $procedures = $this->chamberService->getProcedure($chamber->getId());
        $this->assertArrayHasKey('type',$procedures);
        $this->assertArrayHasKey('code',$procedures);
        $this->assertArrayHasKey('message',$procedures);
        $this->assertSame($procedures['code'],404);
        $this->em->remove($chamber);
        $this->em->flush();
    }
    public function testWithProcedure(): void
    {
        $procedures = $this->chamberService->getProcedure(19);
        $this->assertArrayHasKey('type',$procedures);
        $this->assertArrayHasKey('code',$procedures);
        $this->assertArrayHasKey('message',$procedures);
//        $this->assertSame($procedures['code'],200);
    }
    public function testInvalidId(): void
    {
        $procedures = $this->chamberService->getProcedure(993982);
        $this->assertArrayHasKey('type',$procedures);
        $this->assertArrayHasKey('code',$procedures);
        $this->assertArrayHasKey('message',$procedures);
        $this->assertSame($procedures['code'],404);
    }
}
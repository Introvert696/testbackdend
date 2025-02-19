<?php

namespace App\Tests\Services\ChambersService;

use App\Entity\Chambers;
use App\Tests\Services\BaseService;

class UpdateTest extends BaseService
{
    public function testCurrent() : void
    {
        $chamber = new Chambers();
        $chamber->setNumber(38);
        $this->em->persist($chamber);
        $this->em->flush();
        $data = [
            "number"=>543234
        ];
        $response = $this->chamberService->update($chamber->getId(),json_encode($data));
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertSame($response['type'],"Updated");
        $this->assertSame($response['code'],200);
        $this->em->remove($chamber);
        $this->em->flush();

    }
    public function testConflict() : void
    {
        $chamber = new Chambers();
        $chamber->setNumber(627);
        $this->em->persist($chamber);
        $this->em->flush();
        $data = [
            "number"=>67
        ];
        $response = $this->chamberService->update($chamber->getId(),json_encode($data));
        $this->em->remove($chamber);
        $this->em->flush();
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertSame($response['type'],"Error");
        $this->assertSame($response['code'],502);


    }
    public function testNotFound() : void
    {

        $data = [
            "number"=>673
        ];
        $response = $this->chamberService->update(0,json_encode($data));
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertSame($response['type'],"Error");
        $this->assertSame($response['code'],502);

    }

}
<?php
namespace App\Tests\Services\PatientsServices;

use App\Entity\Patients;
use App\Tests\Services\BaseService;

class UpdateTest extends BaseService
{
    public function testValidData(): void
    {
        $patient = new Patients();
        $patient->setName("Unit test user");
        $patient->setCardNumber(6724);
        $this->em->persist($patient);
        $this->em->flush();


        $data = [
            "name"=>"test test test",
            "chamber" => 3
        ];
        $data = json_encode($data);
        $response = $this->patientsServices->update($patient->getId(),$data);

        $this->em->remove($patient);
        $this->em->flush();

        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);
        $this->assertArrayHasKey('data',$response);

        $this->assertSame(200,$response['code']);
        $this->assertSame('Updated',$response['type']);
        $this->assertSame('test test test',$response['data']->getName());
    }
    public function testNotValidData(): void
    {
        $patient = new Patients();
        $patient->setName("Unit test user");
        $patient->setCardNumber(4632);
        $this->em->persist($patient);
        $this->em->flush();
        $data = [
        ];
        $data = json_encode($data);
        $response = $this->patientsServices->update($patient->getId(),$data);
        $this->em->remove($patient);
        $this->em->flush();
        $this->assertArrayHasKey('type',$response);
        $this->assertArrayHasKey('code',$response);
        $this->assertArrayHasKey('message',$response);

        $this->assertSame(404,$response['code']);
        $this->assertSame('Not found',$response['type']);
    }
    public function testNotValidChamber(): void
    {
        $testName = "test frm unit test";
       $patient = [
           "name" => "test user",
           "card_number" => 58
       ];
        $response= $this->patientsServices->store(json_encode($patient));
        if($response['type']=== 'Error'){
            $this->assertSame(502,$response['code']);
        }
        else{
            $patient = $response['data'];
            $data = [
                "name"=>$testName,
                "chamber" => 0
            ];
            $data = json_encode($data);
            $result = $this->patientsServices->update($patient->getId(),$data);
            $this->patientsServices->delete($patient->getId());
            $this->assertArrayHasKey('type',$result);
            $this->assertArrayHasKey('code',$result);
            $this->assertArrayHasKey('message',$result);

            $this->assertSame(404,$result['code']);
            $this->assertSame('Not found',$result['type']);
        }
    }

}
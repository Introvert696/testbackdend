<?php
namespace App\Tests\Services\AdaptersService;

use App\Entity\Patients;
use App\Tests\Services\BaseService;

class PatientToPatientResponseDTOTest extends BaseService
{
    public function testValid(): void
    {
        $patient = new Patients();
        $patient->setName("Test");
        $patient->setCardNumber(333);
        $patientResponse = $this->adapterService->patientToPatientResponseDTO($patient);

        $this->assertNotNull($patientResponse);
        $this->assertObjectHasProperty('name',$patientResponse);
        $this->assertObjectHasProperty('card_number',$patientResponse);
        $this->assertObjectHasProperty('chamber',$patientResponse);
        $this->assertObjectHasProperty('procedures',$patientResponse);
        $this->assertSame($patientResponse->getName(),"Test");
        $this->assertSame($patientResponse->getCard_number(),333);
    }
    public function testNotValid(): void
    {
        $patient = new Patients();
        $patientResponse = $this->adapterService->patientToPatientResponseDTO($patient);
        $this->assertFalse($patientResponse);
    }

}
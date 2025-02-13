<?php
namespace App\Tests\Services\AdaptersService;

use App\Entity\Patients;
use App\Services\AdaptersService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PatientToPatientResponseDTOTest extends KernelTestCase
{
    private $container;
    private $adapterService;
    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
        $this->adapterService = $this->container->get(AdaptersService::class);
    }
    public function testMain(): void
    {
        $patient = new Patients();
        $patient->setName("Test");
        $patient->setCardNumber(333);

        $patientResponse = $this->adapterService->patientToPatientResponseDTO($patient);

        $this->assertObjectHasProperty('name',$patientResponse);
        $this->assertObjectHasProperty('card_number',$patientResponse);
        $this->assertObjectHasProperty('chamber',$patientResponse);
        $this->assertObjectHasProperty('procedures',$patientResponse);
    }

}
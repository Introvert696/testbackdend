<?php

namespace App\Tests\Service;


use App\Entity\Patients;
use App\Entity\ProcedureList;
use App\Repository\PatientsRepository;
use App\Services\PatientsServices;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PatientsServiceTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $patient = new Patients();
        $patient->setName("anton");
        $patient->setCardNumber(123123);


        $patientRepository = $this->createMock(PatientsRepository::class);
        $patientRepository->method('findAll')
            ->willReturn([
                (object)$patient,
                (object)$patient
            ]);

        $container->set(PatientsRepository::class,$patientRepository);

        $patientService = $container->get(PatientsServices::class);

        $result = $patientService->all();
//        dd($result);

        $this->assertArrayHasKey('data',$result);

        $this->assertNotEmpty($result);

    }
}

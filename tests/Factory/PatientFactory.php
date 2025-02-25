<?php

namespace App\Tests\Factory;

use App\Entity\Patients;
use Doctrine\ORM\EntityManagerInterface;

class PatientFactory
{
    public readonly EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(): Patients
    {
        $patients = new Patients();
        $patients->setName("Test user from factory");
        $patients->setCardNumber(122);

        return $this->upload($patients);
    }
    public function upload(Patients $patients): Patients
    {
        $this->em->persist($patients);
        $this->em->flush();
        return $patients;
    }
}
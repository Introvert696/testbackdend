<?php

namespace App\DataFixtures;

use App\Entity\Patients;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PatientsFixtures extends Fixture
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    )
    {
    }
    public function load(ObjectManager $manager): void
    {
        $limit = $this->parameterBag->get('fixture_rate');
        $faker = Factory::create();

        for($i=0;$i<$limit;$i++){
            $patient = new Patients();
            $patient->setName($faker->firstName .' '. $faker->lastName);
            $patient->setCardNumber($faker->unique()->randomNumber(5, true));
            $manager->persist($patient);
            $this->addReference('patients_'.$i,$patient);
        }


        $manager->flush();
    }
}

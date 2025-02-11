<?php

namespace App\DataFixtures;

use App\Entity\Chambers;
use App\Entity\Patients;
use App\Entity\ProcedureList;
use App\Entity\Procedures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProcedureListFixtures extends Fixture implements DependentFixtureInterface
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

        // Вообще вот эти настройки количества лучше вынести в какойнить env для ручной настройки, пока оставлю под 20
        for($i= 0 ;$i<$limit;$i++){
            $procList = new ProcedureList();
            $procList->setProcedures($this->getReference('procedure_'.$faker->numberBetween(0,$limit-1),Procedures::class));
            $procList->setQueue($faker->numberBetween(1,30));
            $procList->setStatus($faker->boolean(50));

            if($faker->boolean(50)){
                $procList->setSourceType('chambers');
                // тут id получать т.е. генерить от 0 то 20 получать либо палату либо пациента в ифе, и получать их id
                $chamber = $this->getReference('chambers_'.$faker->numberBetween(0,$limit-1),Chambers::class);
                $procList->setSourceId($chamber->getId());
            }
            else{
                $procList->setSourceType('patients');
                $patient = $this->getReference('patients_'.$faker->numberBetween(0,$limit-1),Patients::class);
                $procList->setSourceId($patient->getId());
            }
            $manager->persist($procList);
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            PatientsFixtures::class,
            ChambersFixtures::class
        ];
    }
}

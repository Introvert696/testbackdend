<?php

namespace App\DataFixtures;

use App\Entity\Chambers;
use App\Entity\ChambersPatients;
use App\Entity\Patients;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ChambersPatientsFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ){}
    public function load(ObjectManager $manager): void
    {
        $limit = $this->parameterBag->get('fixture_rate');
        $faker = Factory::create('ru_RU');
        for($i=0;$i<$limit;$i++){
            $cp = new ChambersPatients();
            $cp->setPatients($this->getReference(
                'patients_'.$faker->unique()->numberBetween(0,$limit-1),
                Patients::class)
            );
            $cp->setChambers($this->getReference(
                'chambers_'.$faker->numberBetween(0,$limit-1),
                Chambers::class)
            );
            $manager->persist($cp);
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

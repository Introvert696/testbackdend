<?php

namespace App\DataFixtures;

use App\Entity\Chambers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ChambersFixtures extends Fixture
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ){}
    public function load(ObjectManager $manager): void
    {
        $limit = $this->parameterBag->get('fixture_rate');
        $faker = Factory::create();
        for($i = 0; $i<$limit; $i++){
            $chamber = new Chambers();
            $chamber->setNumber(
                $faker->unique()->numberBetween(0,10000)
            );
            $manager->persist($chamber);
            $this->addReference('chambers_'.$i,$chamber);
        }
        $manager->flush();
    }
}

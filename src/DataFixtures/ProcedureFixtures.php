<?php

namespace App\DataFixtures;

use App\Entity\Procedures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProcedureFixtures extends Fixture
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
            $procedure = new Procedures();
            $procedure->setTitle($faker->word.$faker->unique()->numberBetween(0,1000));
            $procedure->setDescription($faker->sentence);
            $manager->persist($procedure);
            $this->addReference('procedure_'.$i,$procedure);
        }

        $manager->flush();
    }
}

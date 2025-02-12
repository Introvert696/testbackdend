<?php

namespace App\Tests\Factory;

use App\Entity\Chambers;
use Doctrine\ORM\EntityManagerInterface;

class ChamberFactory
{
    public readonly EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(int $number): Chambers
    {
        $chamber = new Chambers();
        $chamber->setNumber($number);
        return $this->upload($chamber);
    }
    public function upload(Chambers $chamber): Chambers
    {
        $this->em->persist($chamber);
        $this->em->flush();
        return $chamber;
    }
}
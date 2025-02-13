<?php

namespace App\Tests\Factory;

use App\Entity\Procedures;
use Doctrine\ORM\EntityManagerInterface;

class ProcedureFactory
{
    public readonly EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(string $title): Procedures
    {
        $procedures = new Procedures();
        $procedures->setTitle($title);
        $procedures->setDescription("Procedures from factory");

        return $this->upload($procedures);
    }
    public function upload(Procedures $procedures): Procedures
    {
        $this->em->persist($procedures);
        $this->em->flush();
        return $procedures;
    }
}
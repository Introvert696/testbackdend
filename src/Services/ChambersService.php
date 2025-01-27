<?php

namespace App\Services;

use App\Entity\Chambers;
use App\Entity\ChambersPatients;
use App\Repository\ChambersRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChambersService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly JsonResponseHelper $jsonResponseHelpers,
        private readonly ChambersRepository $chambersRepository,
    )
    {}
    public function get($id): array
    {
        $patients = [];
        $chamber = $this->chambersRepository->find($id);
        $chamberPatients = $chamber->getChambersPatients()->getValues();

        foreach ($chamberPatients as $cp){
            $patients[] = $cp->getPatients();
        }
        $data['chamber'] = $chamber;
        $data['patients'] = $patients;

        return $this->jsonResponseHelpers->generate('Ok',200,'Chamber and he patients',$data);
    }
    public function getProcedure($id): array
    {
        $query = $this->em->createQuery(
            'SELECT pr.id,pr.title from App\Entity\ProcedureList pl
                  JOIN pl.procedures pr
                  JOIN pl.patients p
                  JOIN p.chambersPatients cp
                  join cp.chambers c
                  where c.id = 1'
        );
        $result =$query->getResult();

        return $this->jsonResponseHelpers->generate('Ok',200,'Procedures, chamber - '.$id ,$result);
    }
}
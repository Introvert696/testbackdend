<?php

namespace App\Services;

use App\Entity\Procedures;
use App\Repository\ChambersRepository;
use App\Repository\PatientsRepository;
use App\Repository\ProcedureListRepository;
use App\Repository\ProceduresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProceduresService
{
    public function __construct(
        private readonly ProceduresRepository $proceduresRepository,
        private readonly JsonResponseHelper $jsonResponseHelper,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly PatientsRepository $patientsRepository,
        private readonly ChambersRepository $chambersRepository,
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
    )
    {}
    public function all(): array
    {
        $procedures = $this->proceduresRepository->findAll();
        return $this->jsonResponseHelper->generate('OK',200,'procedures',$procedures);
    }
    public function about(int $id): array
    {
        $response = [];
        $procedure = $this->proceduresRepository->find($id);
        $response['procedure']=$procedure;
        $procedureList = $this->procedureListRepository->findBy([
            'procedures'=>$procedure->getId(),
            'status' => 1
        ]);
        foreach ($procedureList as $pl){

            if($pl->getSourceType()=='patients'){
                $patient = $this->patientsRepository->find($pl->getSourceId());
                $response['patients'][]=$patient;
            }
            else if($pl->getSourceType()=='chambers'){
                $chamber = $this->chambersRepository->find($pl->getSourceId());
                $response['chamber'][]=$chamber;
            }
        }

        return $this->jsonResponseHelper->generate('OK',200,'about procedure info',$response);
    }
    public function store($data):array{
        // add method findOrCreate
        // deserialize data and work
        $data = $this->serializer->deserialize($data,Procedures::class,'json');
        if($data->getTitle() and $data->getDescription()){
            $this->em->persist($data);
            $this->em->flush();
        }
        return $this->jsonResponseHelper->generate('create',200,'procedure has been create',Array($data));
    }
    public function update(int $id,$data): array
    {
        $data = $this->serializer->deserialize($data,Procedures::class,'json');
        $procedure = $this->proceduresRepository->find($id);
        $procedure->setTitle($data->getTitle());
        $procedure->setDescription($data->getDescription());
        $this->em->flush();
        return $this->jsonResponseHelper->generate('Update',200,'procedure has been updated',Array($procedure));
    }
    public function delete(int $id): array
    {
        $procedure = $this->proceduresRepository->find($id);
        $this->em->remove($procedure);
        $this->em->flush();
        return $this->jsonResponseHelper->generate('Delete',200,'procedure has been delete');
    }
}
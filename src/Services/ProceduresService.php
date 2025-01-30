<?php

namespace App\Services;

use App\Entity\Chambers;
use App\Entity\Patients;
use App\Entity\ProcedureList;
use App\Entity\Procedures;
use App\Repository\ProceduresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProceduresService
{
    public function __construct(
        // get repository from entity manager
        private readonly JsonResponseHelper $jsonResponseHelper,
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly ProceduresRepository $proceduresRepository,
    )
    {}
    public function all(): array
    {
        $procedures = $this->proceduresRepository->findAll();
        return $this->jsonResponseHelper->generate('OK',200,'procedures',$procedures);
    }
    public function about(int $id): array
    {
        $procedureListRepository = $this->em->getRepository(ProcedureList::class);
        $response = [];
        $procedure = $this->proceduresRepository->find($id);
        $response['procedure']=$procedure;
        $procedureList = $procedureListRepository->findBy([
            'procedures'=>$procedure->getId(),
            'status' => 1
        ]);
        $response['data'] = $this->getSourceItem($procedureList);

        return $this->jsonResponseHelper->generate('OK',200,'about procedure info',$response);
    }
    public function getSourceItem(array $procedureList): array
    {
        $chambersRepository = $this->em->getRepository(Chambers::class);
        $response = [];

        foreach ($procedureList as $pl){
            if($pl->getSourceType()=='patients'){
                $patient = $this->proceduresRepository->find($pl->getSourceId());
                $response['patients'][]=$patient;
            }
            else if($pl->getSourceType()=='chambers'){
                $chamber = $chambersRepository->find($pl->getSourceId());
                $response['chamber'][]=$chamber;
            }
        }
        return $response;
    }
    public function store($data):array{
        $data = $this->serializer->deserialize($data,Procedures::class,'json');

        if($data->getTitle() and $data->getDescription()){
            $issetProcedure = $this->proceduresRepository->findBy([
                'title' => $data->getTitle()
            ]);
            if(!$issetProcedure){
                $this->em->persist($data);
                $this->em->flush();
            }
            else{
                return $this->jsonResponseHelper->generate('Conflict',402,'title has busy',Array($issetProcedure));
            }
        }
        return $this->jsonResponseHelper->generate('create',200,'procedure has been create',Array($data));
    }
    public function update(int $id,$data): array
    {
        $data = $this->serializer->deserialize($data,Procedures::class,'json');
        $procedure = $this->proceduresRepository->find($id);
        if($procedure){
            if($data->getTitle() and $data->getDescription()){
                $procedure->setTitle($data->getTitle());
                $procedure->setDescription($data->getDescription());
                $this->em->flush();
                $response = $this->jsonResponseHelper->generate('Update',200,'procedure has been updated',Array($procedure));
            }
            else {
                $response = $this->jsonResponseHelper->generate('Empty data',400,'Field not filled',Array($procedure));
            }

        }
        else {
            $response = $this->jsonResponseHelper->generate('Not Found',404,'Procedure not found');
        }
        return $response;
    }
    public function delete(int $id): array
    {
        $procedure = $this->proceduresRepository->find($id);
        $this->em->remove($procedure);
        $this->em->flush();
        return $this->jsonResponseHelper->generate('Delete',200,'procedure has been delete');
    }
}
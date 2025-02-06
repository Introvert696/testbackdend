<?php

namespace App\Services;

use App\Entity\Procedures;
use App\Repository\ProcedureListRepository;
use App\Repository\ProceduresRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProceduresService
{
    public function __construct(
        private readonly JsonResponseHelper $jsonResponseHelper,
        private readonly EntityManagerInterface $em,
        private readonly ProceduresRepository $proceduresRepository,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly AdaptersService $adaptersService
    )
    {}
    public function all(): array
    {
        $procedures = $this->proceduresRepository->findAll();
        return $this->jsonResponseHelper->generate('OK',200,'procedures',$procedures);
    }
    public function about(int $id): array
    {
        $procedure = $this->proceduresRepository->find($id);
        if(!$procedure){
            return $this->jsonResponseHelper->generate('Not found',404,'Procedure not found');
        }
        $procedureResponse = $this->adaptersService->procedureToProcedureResponseDTO($procedure);
        $entities = $this->procedureListRepository->findBy([
            'source_id'=>$procedure->getId(),
            'status' => 1
        ]);
        if(!$entities) {
            return $this->jsonResponseHelper->generate('OK', 200, 'Procedure ingo', $procedureResponse);
        }
        foreach ($entities as $et){
            $procedureResponse->addEntity($this->adaptersService->procListToProcListRespDTO($et));
        }

        return $this->jsonResponseHelper->generate('OK',200,'about procedure info',$procedureResponse);
    }
    // переделать
    public function store($data):array{
        $data = $this->jsonResponseHelper->checkData($data,'App\Entity\Procedures');
        $data = $this->validator($data);
        if(!$data){
            return $this->jsonResponseHelper->generate('Error',422,'check your fields');
        }
        $issetProcedure = $this->proceduresRepository->findBy([
            'title' => $data->getTitle()
        ]);
        if($issetProcedure){
            return $this->jsonResponseHelper->generate('Conflict',409,'title has exists',$this->jsonResponseHelper->first($issetProcedure));
        }
        $this->em->persist($data);
        $this->em->flush();

        return $this->jsonResponseHelper->generate('create',200,'Procedure has been create',$data);
    }
    public function update(int $id,$data): array
    {
        $procedure = $this->proceduresRepository->find($id);
        if(!$procedure) {
            return $this->jsonResponseHelper->generate('Not Found',404,'Procedure not found');
        }
        $data = $this->jsonResponseHelper->checkData($data,'App\Entity\Procedures');
        $data = $this->validator($data);
        if(!$data){
            return $this->jsonResponseHelper->generate('Error',422,'Check your fields');
        }
        $procedure->setTitle($data->getTitle());
        $procedure->setDescription($data->getDescription());
        $this->em->flush();

        return $this->jsonResponseHelper->generate('Update',200,'Procedure has been updated',$procedure);
    }
    public function delete(int $id): array
    {
        $procedure = $this->proceduresRepository->find($id);
        if(!$procedure){
           return $this->jsonResponseHelper->generate('Not Found',404,'Procedure not found');
        }
        $this->em->remove($procedure);
        $this->em->flush();
        return $this->jsonResponseHelper->generate('Delete',200,'procedure has been delete');
    }

    public function validator($data): null|Procedures
    {
        if($data == null){
            return null;
        }
        if(($data->getTitle()!==null)and($data->getDescription()!==null)){
            return $data;
        }else{
            return null;
        }
    }
}
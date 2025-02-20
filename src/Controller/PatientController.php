<?php

namespace App\Controller;

use App\DTO\ResponseDTO;
use App\Repository\ChambersRepository;
use App\Repository\PatientsRepository;
use App\Repository\ProcedureListRepository;
use App\Services\AdaptersService;
use App\Services\ChambersPatientsService;
use App\Services\ResponseHelper;
use App\Services\ValidateService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/patients')]
final class PatientController extends AbstractController
{
    public function __construct(
       private readonly ResponseHelper $responseHelper,
       private readonly EntityManagerInterface $em,
       private readonly PatientsRepository $patientsRepository,
       private readonly ProcedureListRepository $procedureListRepository,
       private readonly AdaptersService $adaptersService
    ){}
    #[Route('/',name:'index_patients',methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return all patient',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Return all patient",
                "data" =>[
                    [
                        "id"=>1,
                        "name" =>'Мишустин Алексей Игоревич',
                        "card_number" =>23123
                    ],
                    [
                        "id"=>1,
                        "name" =>'Мишустин Алексей Игоревич',
                        "card_number" =>23123
                    ]
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Patient")]
    public function index(): JsonResponse
    {
        $response = $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            'return all patient',
            $this->patientsRepository->findAll());
        return $this->json($response);
    }
    #[Route('/{id}', name: 'show_patients', defaults: ['id'=>null], methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return all patient',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Return all patient",
                "data" =>[
                    [
                        "id"=>1,
                        "name" =>'Мишустин Алексей Игоревич',
                        "card_number" =>23123
                    ],
                    [
                        "id"=>1,
                        "name" =>'Мишустин Алексей Игоревич',
                        "card_number" =>23123
                    ]
                ]
            ]
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Patient not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Patient not found",
            ]
        ),
    )]
    #[OA\Tag(name:"Patient")]
    public function show(int|null $id,): JsonResponse
    {
        $patient = $this->patientsRepository->find($id);
        $procList = $this->procedureListRepository->findBy([
            'source_type'=>'patients',
            'source_id'=>$id
        ]);
        if(!$patient){
            $response = $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                "Patient not found");
            return $this->json($response,$response['code']);
        }
        $patient = $this->adaptersService->patientToPatientResponseDTO(
            $patient,
            $procList
        );

        $response = $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            "Patient info",
            $patient);

        return $this->json($response,$response['code']);
    }
    #[Route(name:'store_patients',methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: 'object',
            ),
            example: [
                "name"=>"Елисеев Михаил Васильевич",
                "card_number"=>3333
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Patient has been created',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Patient has been created",
                "data" =>[
                    "id" => 24,
                    "name" => "Елисеев Михаил Васильевич",
                    "card_number" => 3333
                ]
            ]
        ),
    )]
    #[OA\Response(
        response: 409,
        description: 'Conflict',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Conflict",
                "code"=>409,
                "message" => "card_number already exists",
            ]
        ),
    )]
    #[OA\Response(
        response: 502,
        description: 'Error fields',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>502,
                "message" => "Check fields",
            ]
        ),
    )]
    #[OA\Tag(name:"Patient")]
    public function store(
        Request $request,
        ValidateService $validateService,
    ): JsonResponse
    {
        $data = $request->getContent();
        $data = $this->responseHelper
            ->checkData($data,'App\Entity\Patients');
        $result = $this->patientsRepository->findBy([
            'card_number'=>$data?->getCardNumber()
        ]);
        if(!$data or !$validateService->patients($data) or $result){
            $response = $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_BODY,
                'Check body');
            return $this->json($response,$response['code']);
        }
        $this->em->persist($data);
        $this->em->flush();

        $response = $this->responseHelper->generate(
            'Created',
            ResponseHelper::STATUS_OK,
            'patient has been created',
            $data);
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'update_patients', defaults: ['id'=>null], methods: ['PATCH'])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: 'object',
            ),
            example: [
               "name"=>"Елисеев Михаил Васильевич",
                "chamber"=>3
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Patient not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Patient not found",
            ]
        ),
    )]
    #[OA\Response(
        response: 402,
        description: 'Fields not filled',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>402,
                "message" => "Fields not filled",
            ]
        ),
    )]
    #[OA\Response(
        response: 422,
        description: 'Fields not filled',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>422,
                "message" => "Chamber not found",
            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Patient has been updated',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Patient has been updated",
                "data" =>[
                    "id"=>8,
                    "name"=>"Елисеев Михайл Васильевич",
                    "card_number"=>234234
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Patient")]
    public function update(
        Request $request,
        int|null $id,
        ChambersRepository $chambersRepository,
        ChambersPatientsService $chambersPatientsService,
    ): JsonResponse
    {
        $data = $request->getContent();
        $updatedData = $this->responseHelper
            ->checkData($data,'App\DTO\PatientDTO');
        $patient = $this->patientsRepository->find($id);
        $chamber = $updatedData->chamber!=null?
            $chambersRepository->find($updatedData?->chamber):null;
        if(!$updatedData ){
            $response = $this->responseHelper->generate(
                'Error',
                ResponseHelper::STATUS_NOT_VALID_FIELDS,
                'Check you field');
            return $this->json($response,$response['code']);
        }
        if(!$patient){
            $response = $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Patient - not found');
            return $this->json($response,$response['code']);
        }
        if (!$chamber){
            $response = $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                'Patient - not found');
            return $this->json($response,$response['code']);
        }
        $patient->setName($updatedData->name ?? $patient->getName());
        $chamberPatients = $patient->getChambersPatients();
        if ($chamberPatients){
            $chamberPatients->setChambers($chamber);
        } else{
            $chamberPatients = $chambersPatientsService->create(
                $patient,
                $chamber
            );
            $this->em->persist($chamberPatients);
        }
        $this->em->flush();

        $response = $this->responseHelper->generate(
            'Updated',
            ResponseHelper::STATUS_OK,
            'Patient has been updated',
            $this->adaptersService->patientToPatientResponseDTO($patient));
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'delete_patients', defaults: ['id'=>null], methods: ['DELETE'])]
    #[OA\Response(
        response: 404,
        description: 'Patient not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Patient not found",
            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Return all patient',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Patient has been delete",
            ]
        ),
    )]
    #[OA\Tag(name:"Patient")]
    public function delete(int|null $id): JsonResponse
    {
        $patient = $this->patientsRepository->getMore($id);
        $procedureList = $this->procedureListRepository->findBy([
            'source_type' => 'patients',
            'source_id' => $id
        ]);

        if(!$patient){
            $response = $this->responseHelper->generate(
                'Not found',
                ResponseHelper::STATUS_NOT_FOUND,
                "Patient not found");
            return $this->json($response,$response['code']);
        }
        foreach ($procedureList as $pl){
            $this->em->remove($pl);
        }
        $this->em->remove($this->responseHelper->first($patient));
        $this->em->flush();

        $response = $this->responseHelper->generate(
            'Ok',
            ResponseHelper::STATUS_OK,
            "Patient has been delete");
        return $this->json($response,$response['code']);
    }
}

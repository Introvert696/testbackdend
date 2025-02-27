<?php

namespace App\Controller;

use App\DTO\Chamber\ChamberResponseDTO;
use App\DTO\ResponseDTO;
use App\Exception\ApiResponseException;
use App\Repository\ChambersRepository;
use App\Repository\ProcedureListRepository;
use App\Services\AdaptersService;
use App\Services\ResponseFabric;
use App\Services\ResponseHelper;
use App\Services\ValidateService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/chambers')]
final class ChamberController extends AbstractController
{
    public function __construct(
        private readonly ResponseHelper          $responseHelper,
        private readonly ChambersRepository      $chambersRepository,
        private readonly ProcedureListRepository $procedureListRepository,
        private readonly EntityManagerInterface  $entityManager,
        private readonly AdaptersService         $adaptersService,
        private readonly ValidateService         $validateService,
        private readonly ResponseFabric          $responseFabric,
    ){}
    #[OA\Response(
            response: 200,
            description: 'Return all chambers',
            content: new OA\JsonContent(
                ref: new Model(type:ResponseDTO::class),
                type: 'object',
                example: [
                    "type"=>"ok",
                    "code"=>200,
                    "message" => "chamber and patients",
                    "data" =>[
                       [
                           "id"=>1,
                           "number" =>233,
                       ],
                        [
                            "id"=>1,
                            "number" =>233,
                        ]
                    ]
                ]
        )
    )]
    #[OA\Tag(name:"Chamber")]
    #[Route('/', name: 'index_chambers', methods: ["GET"])]
    public function index(): JsonResponse
    {
        $response = $this->responseFabric->getResponse(
            ResponseFabric::RESPONSE_TYPE_OK,
            'All chambers',
            $this->chambersRepository->findAll());
        return $this->json($response,$response['code']);
    }

    #[OA\Response(
        response: 200,
        description: 'Return all chambers',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"ok",
                "code"=>200,
                "message" => "chamber and he patients",
                "data" =>[
                    "id"=>1,
                    "number" =>233,
                    "patients" =>[]
                ]
            ]
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Return all chambers',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Chamber not found",
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    #[Route('/{id}', name: 'show_chambers',requirements: ['id'=>Requirement::DIGITS], methods: ["GET"])]
    public function show(int $id): JsonResponse
    {
        $chamberResponse = new ChamberResponseDTO();
        $patients = [];
        $foundChamber = $this->chambersRepository->find($id);
        if(!$foundChamber){
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_NOT_FOUND,
                'Chamber - not found');
            return $this->json($response,$response['code']);
        }
        $chamberPatients = $foundChamber->getChambersPatients()->getValues();
        if($chamberPatients){
            foreach ($chamberPatients as $cp){
                $patients[] = $cp->getPatients();
            }
            $chamberResponse->setPatients($patients);
        }
        $chamberResponse->setId($foundChamber->getId());
        $chamberResponse->setNumber($foundChamber->getNumber());
        $response = $this->responseFabric->getResponse(
            ResponseFabric::RESPONSE_TYPE_OK,
            'Info about chamber',
            $chamberResponse);
        return $this->json($response,$response['code']);
    }

    #[OA\Response(
        response: 200,
        description: 'Return all chambers',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"ok",
                "code"=>200,
                "message" => "chamber and he patients",
                "data" =>[
                    "id"=>1,
                    "number" =>233,
                    "data" =>[
                        [
                            "id"=>1,
                            "title" =>'Эхокардиография',
                            "desc" =>'Взять ',
                            "queue" =>1,
                            "status" =>false
                        ],
                        [
                            "id"=>1,
                            "title" =>'Эхокардиография',
                            "desc" =>'Взять ',
                            "queue" =>1,
                            "status" =>false
                        ]
                    ]
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Chamber procedures",
            ]
        ),
    )]
    #[Route('/{id}/procedures', name: 'show_chambers_procedures',requirements: ['id'=>Requirement::DIGITS], methods: ["GET"])]
    public function showProcedures(int $id): JsonResponse
    {
        $chamberProcedures = [];
        $foundProcedureLists = $this->procedureListRepository->findBy([
            'source_type'=>'chambers',
            'source_id'=>$id
        ]);
        foreach ($foundProcedureLists as $pl){
            $chamberProcedures[] = $this->adaptersService
                ->procedureListToChamberProcedureDTO($pl);
        }
        if(!$chamberProcedures){
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_NOT_FOUND,
                'Patient - not found');
            return $this->json($response,$response['code']);
        }
        $response = $this->responseFabric->getResponse(
            ResponseFabric::RESPONSE_TYPE_OK,
            'Chamber - '.$id.', have next procedure:' ,
            $chamberProcedures);
        return $this->json($response,$response['code']);

    }

    /**
     * @throws ApiResponseException
     */
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: 'object',
            ),
            example: [
                [
                    'procedure_id' => 1,
                    'queue'=>3,
                    'status'=>true
                ],
                [
                    'procedure_id' => 2,
                    'queue'=>3,
                    'status'=>true
                ],
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Chambers not found",
            ]
        ),
    )]
    #[OA\Response(
        response: 402,
        description: 'Fields Error',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>404,
                "message" => "Check fields",
            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Updated',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Procedure list has been update",
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    #[Route('/{id}/procedures', name: 'update_chambers_procedures',requirements: ['id'=>Requirement::DIGITS], methods: ["POST"])]
    public function updateProcedures(Request $request, int $id): JsonResponse
    {
        $chamberProcedures = [];
        $foundChamber = $this->chambersRepository->find($id);

        $checkedProcListDTOs = $this->responseHelper->checkRequest($request->getContent(), 'App\DTO\Chamber\ProcListDTO[]');
        $foundProcedureLists = $this->procedureListRepository->findBy([
            'source_type' => 'chambers',
            'source_id' => $id
        ]);
        if (!$foundChamber) {
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_NOT_FOUND,
                'Chamber - not found');
            return $this->json($response,$response['code']);
        }
        if (!$checkedProcListDTOs) {
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_NOT_VALID);

            return $this->json($response,$response['code']);
        }
        if ($foundProcedureLists) {
            foreach ($foundProcedureLists as $pl) {
                $this->entityManager->remove($pl);
            }
        }
        foreach ($checkedProcListDTOs as $d) {
            $checkedProcedureList = $this->validateService
                ->procedureListWithProcedure($d);
            if (!$checkedProcedureList) {
                $response = $this->responseFabric->getResponse(
                    ResponseFabric::RESPONSE_TYPE_NOT_VALID);

                return $this->json($response,$response['code']);
            }
            $procedureList = $this->adaptersService
                ->procListDtoToProcList($checkedProcedureList, $id);
            $this->entityManager->persist($procedureList);
            $chamberProcedures[] = $this->adaptersService
                ->procedureListToChamberProcedureDto($procedureList);
        }
        $this->entityManager->flush();

        return $this->json($this->responseFabric->getResponse(
            ResponseFabric::RESPONSE_TYPE_OK,
            'Chambers procedure has been update',
            $chamberProcedures));
    }

    /**
     * @throws ApiResponseException
     */
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: 'object',
            ),
            example:
                [
                    'number' => 1,
                ],
        ))]
    #[OA\Response(
        response: 402,
        description: 'Error',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>402,
                "message" => "Check request body",
            ]
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Error',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>400,
                "message" => "Chamber is exists",
                "data"=>[
                    'id' => 23,
                    "number"=>1
                ]
            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Create',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Create",
                "code"=>200,
                "message" => "Chamber has been create",
                "data"=>[
                    'id' => 24,
                    "number"=>11
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    #[Route(name: 'store_chambers', methods: ["POST"])]
    public function store(Request $request): JsonResponse
    {
        $validatedRequestChamber = $this->validateService->chambersRequestData(
            $this->responseHelper->checkRequest($request->getContent(),'App\Entity\Chambers')
        );
        if(!$validatedRequestChamber){
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_NOT_VALID);
        return $this->json($response,$response['code']);
        }
        $foundChamber = $this->chambersRepository->findBy([
            'number' =>$validatedRequestChamber->getNumber()
        ]);
        if($foundChamber){
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_CONFLICT,
                $this->responseHelper->first($foundChamber));
            return $this->json($response,$response['code']);
        }
        $this->entityManager->persist($validatedRequestChamber);
        $this->entityManager->flush();

        $response = $this->responseFabric->getResponse(
            ResponseFabric::RESPONSE_TYPE_OK,
            'Chamber has been create',
            $validatedRequestChamber);
        return $this->json($response,$response['code']);
    }

    /**
     * @throws ApiResponseException
     */
    #[OA\RequestBody(
      required: true,
        content: new OA\JsonContent(
            properties: [
                "number"=>new OA\Property(type:'integer',example: '2')
        ],
            type: "string", example: [
                "number"=>333
              ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Not Found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Chamber not found",
            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Updated',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Updated",
                "code"=>200,
                "message" => "Chamber has been updated",
                "data"=>[
                    "id"=>1,
                    "number" => 333
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    #[Route('/{id}', name: 'update_chambers', methods: ["PATCH"])]
    public function update(Request $request, int|null $id): JsonResponse
    {
        $requestChamber = $this->responseHelper->checkRequest($request->getContent(),'App\Entity\Chambers');
        $foundChamber = $this->chambersRepository->find($id);
        $validateRequestChamber = ( (!$requestChamber) or
            (gettype($requestChamber->getNumber())!=="integer") or
            $this->chambersRepository->findBy([
                'number'=>$requestChamber->getNumber()
            ])
        );
        if(!$foundChamber){
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_NOT_FOUND,
                'Chamber not found');
            return $this->json($response,$response['code']);
        }
        if($validateRequestChamber){
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_NOT_VALID);
            return $this->json($response,$response['code']);
        }
        $foundChamber->setNumber($requestChamber->getNumber());
        $this->entityManager->flush();

        $response = $this->responseFabric->getResponse(
            ResponseFabric::RESPONSE_TYPE_OK,
            'Chamber has been update',
            $foundChamber);
        return $this->json($response,$response['code']);
    }

    #[OA\Response(
        response: 202,
        description: 'Return all chambers',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Delete",
                "code"=>202,
                "message" => "Chamber has been delete",
            ]
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not Found",
                "code"=>404,
                "message" => "Chamber - not found",
            ]
        ),
    )]
    #[OA\Tag(name:"Chamber")]
    #[Route('/{id}', name: 'delete_chambers', requirements: ['id'=>Requirement::DIGITS], methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $foundChamber= $this->chambersRepository->find($id);
        if(!$foundChamber){
            $response = $this->responseFabric->getResponse(
                ResponseFabric::RESPONSE_TYPE_NOT_FOUND,
                'Chamber - not found');
            return $this->json($response,$response['code']);
        }
        $chamberPatient = $foundChamber->getChambersPatients()->getValues();
        if($chamberPatient){
            foreach ($chamberPatient as $cp)
            {
                $this->entityManager->remove($cp);
            }
        }
        $foundProcedureLists = $this->procedureListRepository->findBy([
            'source_id' => $foundChamber->getId(),
            'source_type' => 'chambers'
        ]);
        if($foundProcedureLists){
            foreach ($foundProcedureLists as $procedureList){
                $this->entityManager->remove($procedureList);
            }
        }
        $this->entityManager->remove($foundChamber);
        $this->entityManager->flush();

        $response = $this->responseFabric->getResponse(
            ResponseFabric::RESPONSE_TYPE_OK,
            'Chamber - has been delete');
        return $this->json($response,$response['code']);
    }
}

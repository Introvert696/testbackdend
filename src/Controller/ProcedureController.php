<?php

namespace App\Controller;

use App\DTO\ResponseDTO;
use App\Exception\ApiResponseException;
use App\Repository\ProcedureListRepository;
use App\Repository\ProceduresRepository;
use App\Services\AdaptersService;
use App\Services\ResponseFabric;
use App\Services\ResponseHelper;
use App\Services\ValidateService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/procedures')]
final class ProcedureController extends AbstractController
{
    public function __construct(
        private readonly ResponseHelper $responseHelper,
        private readonly ProceduresRepository $proceduresRepository,
        private readonly EntityManagerInterface $em,
        private readonly ValidateService $validateService,
        private readonly ResponseFabric $responseFabric,
    ){}
    #[OA\Response(
        response: 200,
        description: 'Return all procedures',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Return all procedures",
                "data"=>[
                    [
                        'id'=>1,
                        'title'=>'Электрокардиография',
                        'description'=>'Взять его'
                    ],
                    [
                        'id'=>1,
                        'title'=>'Электрокардиография',
                        'description'=>'Взять его'
                    ],
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    #[Route('/', name: 'index_procedure',methods: ['GET'])]
    public function index(): JsonResponse
    {
        $procedures = $this->proceduresRepository->findAll();
        $response = $this->responseFabric->ok('All procedures',$procedures);
        return $this->json($response);
    }

    /**
     * @throws ApiResponseException
     */
    #[OA\Response(
        response: 200,
        description: 'Return procedure info',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Ok",
                "code"=>200,
                "message" => "Return procedure info",
                "data"=>
                [
                    'id'=>1,
                    'title'=>'Электрокардиография',
                    'description'=>'Взять его',
                    "entityList" => [
                        [
                            "queue"=>3,
                            "status" => true,
                            "sourceId" => 2,
                            "sourceType" => "chambers"
                        ],
                        [
                            "queue"=>3,
                            "status" => true,
                            "sourceId" => 2,
                            "sourceType" => "chambers"
                        ],
                        [
                            "queue"=>3,
                            "status" => true,
                            "sourceId" => 2,
                            "sourceType" => "chambers"
                        ],
                    ]
                ],
            ]
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Procedure not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Procedure not found",
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    #[Route('/{id}', name: 'show_procedure',methods: ['GET'])]
    public function show(
        $id,
        AdaptersService $adaptersService,
        ProcedureListRepository $procedureListRepository
    ): JsonResponse
    {
        $procedure = $this->proceduresRepository->find($id);
        if(!$procedure){
            $this->responseFabric->notFound('Patient - not found');
        }
        $procedureResponse = $adaptersService
            ->procedureToProcedureResponseDTO($procedure);
        $entities = $procedureListRepository->findBy([
            'source_id'=>$procedure->getId(),
            'status' => 1
        ]);
        foreach ($entities as $et){
            $procedureResponse->addEntity(
                $adaptersService->procListToProcListRespDTO($et)
            );
        }
        $response = $this->responseFabric->ok('Procedure info',$procedureResponse);
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
                "title"=>"test",
                "description"=>"test"
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Created',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Created",
                "code"=>200,
                "message" => "Procedure has been create",
                "data" => [
                    "id"=>1,
                    "title"=>"test",
                    "description" => "test"
                ]
            ]
        ),
    )]
    #[OA\Response(
        response: 422,
        description: 'Check fields',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Error",
                "code"=>422,
                "message" => "Check your fields",
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
                "message" => "Title is exists",
                "data"=>[
                    "id"=>19,
                    "title"=>'title',
                    "description" => "desc"
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    #[Route( name: 'store_procedure',methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = $request->getContent();
        $data = $this->validateService->procedures(
            $this->responseHelper->checkData($data,'App\Entity\Procedures')
        );
        if(!$data){
            $this->responseFabric->notValid();
        }
        $issetProcedure = $this->proceduresRepository->findBy([
            'title' => $data->getTitle()
        ]);
        if($issetProcedure){
            $this->responseFabric->conflict(
                $this->responseHelper->first($issetProcedure)
            );
        }
        $this->em->persist($data);
        $this->em->flush();
        $response = $this->responseFabric->ok('Procedure has been create',$data);
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
                "title"=>"Test",
                "description"=>"Test desc"
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Procedure not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Procedure not found",
            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Procedure has been updated',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Update",
                "code"=>200,
                "message" => "Procedure has been updated",
                "data" => [
                    "id" => 1,
                    "title" => "title",
                    "description" => "Description"
                ]
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    #[Route('/{id}', name: 'update_procedure',methods: ['PATCH'])]
    public function update(Request $request,$id): JsonResponse
    {
        $content = $request->getContent();
        $procedure = $this->proceduresRepository->find($id);
        if(!$procedure) {
            $this->responseFabric->notFound('Procedure not found');
        }
        if(!$content){
            $this->responseFabric->notValid();
        }
        $data = $this->validateService->procedures(
            $this->responseHelper->checkData($content,'App\Entity\Procedures')
        );
        if(!$data){
            $this->responseFabric->notValid();
        }
        $procedure->setTitle($data->getTitle());
        $procedure->setDescription($data->getDescription());
        $this->em->flush();

        $response = $this->responseFabric->ok('Procedure has been updated',$procedure);
        return $this->json($response,$response['code']);
    }

    /**
     * @throws ApiResponseException
     */
    #[OA\Response(
        response: 404,
        description: 'Procedure not found',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Not found",
                "code"=>404,
                "message" => "Procedure not found",
            ]
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Delete',
        content: new OA\JsonContent(
            ref: new Model(type:ResponseDTO::class),
            type: "object",
            example: [
                "type"=>"Delete",
                "code"=>200,
                "message" => "Procedure has been delete",
            ]
        ),
    )]
    #[OA\Tag(name:"Procedure")]
    #[Route('/{id}', name: 'delete_procedure',methods: ['DELETE'])]
    public function delete($id): JsonResponse
    {
        $procedure = $this->proceduresRepository->find($id);
        if(!$procedure){
            $this->responseFabric->notFound('Procedure not found');
        }
        $this->em->remove($procedure);
        $this->em->flush();
        $response = $this->responseFabric->ok('Procedure has been delete');

        return $this->json($response,$response['code']);
    }
}

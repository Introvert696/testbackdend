<?php

namespace App\Controller;

use App\DTO\ResponseDTO;
use App\Repository\ProcedureListRepository;
use App\Repository\ProceduresRepository;
use App\Services\AdaptersService;
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
    #[Route('/', name: 'index_procedure',methods: ['GET'])]
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
    public function index(
        ProceduresRepository $proceduresRepository,
        ResponseHelper $responseHelper,
    ): JsonResponse
    {
        $procedures = $proceduresRepository->findAll();
        $response = $responseHelper->generate(
            'OK',
            200,
            'procedures',
            $procedures);
        return $this->json($response);
    }
    #[Route('/{id}', name: 'show_procedure',methods: ['GET'])]
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
    public function show(
        $id,
        ProceduresRepository $proceduresRepository,
        ResponseHelper $responseHelper,
        AdaptersService $adaptersService,
        ProcedureListRepository $procedureListRepository
    ): JsonResponse
    {
        $procedure = $proceduresRepository->find($id);
        if(!$procedure){
            $response = $responseHelper->generate(
                'Not found',
                404,
                'Procedure not found');
            return $this->json($response,$response['code']);
        }
        $procedureResponse = $adaptersService
            ->procedureToProcedureResponseDTO($procedure);
        $entities = $procedureListRepository->findBy([
            'source_id'=>$procedure->getId(),
            'status' => 1
        ]);
        if(!$entities){
            $response = $responseHelper->generate(
                'OK',
                200,
                'Procedure ingo',
                $procedureResponse);
            return $this->json($response,$response['code']);
        }
        foreach ($entities as $et){
            $procedureResponse->addEntity(
                $adaptersService->procListToProcListRespDTO($et)
            );
        }
        $response = $responseHelper->generate(
            'OK',
            200,
            'about procedure info',
            $procedureResponse);
        return $this->json($response,$response['code']);
    }
    #[Route( name: 'store_procedure',methods: ['POST'])]
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
    public function store(
        Request $request,
        ProceduresRepository $proceduresRepository,
        ValidateService $validateService,
        ResponseHelper $responseHelper,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $data = $request->getContent();
        $data = $validateService->procedures($responseHelper->checkData($data,'App\Entity\Procedures')
        );
        if(!$data){
            $response = $responseHelper->generate(
                'Error',
                422,
                'check your fields');
            return $this->json($response,$response['code']);
        }
        $issetProcedure = $proceduresRepository->findBy([
            'title' => $data->getTitle()
        ]);
        if($issetProcedure){
            $response = $responseHelper->generate(
                'Conflict',
                409,
                'title has exists',
                $responseHelper->first($issetProcedure));
            return $this->json($response,$response['code']);
        }
        $em->persist($data);
        $em->flush();

        $response = $responseHelper->generate(
            'create',
            200,
            'Procedure has been create',
            $data);
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'update_procedure',methods: ['PATCH'])]
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
    public function update(
        Request $request,
        $id,
        ProceduresRepository $proceduresRepository,
        ResponseHelper $responseHelper,
        ValidateService $validateService,
        EntityManagerInterface $em
    ): JsonResponse
    {
        $data = $request->getContent();
        $procedure = $proceduresRepository->find($id);
        if(!$procedure) {
            $response = $responseHelper->generate(
                'Not Found',
                404,
                'Procedure not found');
            return $this->json($response,$response['code']);
        }
        $data = $validateService->procedures(
            $responseHelper->checkData($data,'App\Entity\Procedures')
        );
        if(!$data){
            $response = $responseHelper->generate(
                'Error',
                422,
                'Check your fields');
            return $this->json($response,$response['code']);
        }
        $procedure->setTitle($data->getTitle());
        $procedure->setDescription($data->getDescription());
        $em->flush();

        $response = $responseHelper->generate(
            'Update',
            200,
            'Procedure has been updated',
            $procedure);
        return $this->json($response,$response['code']);
    }
    #[Route('/{id}', name: 'delete_procedure',methods: ['DELETE'])]
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
    public function delete(
        ProceduresRepository $proceduresRepository,
        ResponseHelper $responseHelper,
        EntityManagerInterface $em,
        $id
    ): JsonResponse
    {
        $procedure = $proceduresRepository->find($id);
        if(!$procedure){
            $response = $responseHelper->generate(
                'Not Found',
                404,
                'Procedure not found');
            return $this->json($response,$response['code']);
        }
        $em->remove($procedure);
        $em->flush();

        $response = $responseHelper->generate(
            'Delete',
            200,
            'procedure has been delete');
        return $this->json($response,$response['code']);
    }
}

<?php

namespace App\Controller;

use App\DTO\ResponseDTO;
use App\Services\PatientsServices;
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
        private readonly PatientsServices $patientsServices
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
        return $this->json($this->patientsServices->all());
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
    public function get(int|null $id): JsonResponse
    {
        $response = $this->patientsServices->about($id);
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
    public function store(Request $request): JsonResponse
    {
        $response = $this->patientsServices->createOrFind($request->getContent());
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
    public function update(Request $request,int|null $id): JsonResponse
    {
        $response = $this->patientsServices->update($id,$request->getContent());
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
        $response = $this->patientsServices->remove($id);
        return $this->json($response,$response['code']);
    }
}

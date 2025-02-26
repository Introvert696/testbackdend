<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFabric
{
    public const RESPONSE_TYPE_OK = "Ok";
    public const RESPONSE_TYPE_NOT_VALID = "Not valid";
    public const RESPONSE_TYPE_NOT_FOUND = "Not found";
    public const RESPONSE_TYPE_CONFLICT = "Conflict";
    public function __construct(
        private readonly ResponseHelper $responseHelper
    ){}
    private function notFound(string $message): JsonResponse
    {
        $response =  $this->responseHelper->generate(
            self::RESPONSE_TYPE_NOT_FOUND,
            $this->responseHelper::STATUS_NOT_FOUND,
            $message);
        return new JsonResponse($response,$response['code']);
    }

    private function ok(string $message, mixed $data = []): JsonResponse
    {
        $response =  $this->responseHelper->generate(
            self::RESPONSE_TYPE_OK,
            $this->responseHelper::STATUS_OK,
            $message,
            $data ?? []);
        return new JsonResponse($response,$response['code']);
    }

    private function notValid(): JsonResponse
    {
        $response =  $this->responseHelper->generate(
            self::RESPONSE_TYPE_NOT_VALID,
            $this->responseHelper::STATUS_NOT_VALID_FIELDS,
            'Check body');
        return new JsonResponse($response,$response['code']);
    }

    private function conflict(mixed $data): JsonResponse
    {
        $response = $this->responseHelper->generate(
            self::RESPONSE_TYPE_CONFLICT,
            $this->responseHelper::STATUS_CONFLICT,
            'Check fields',
            $data);
        return new JsonResponse($response,$response['code']);
    }

    /**
     * @param string $responseType
     * @param string|null $message
     * @param mixed $data
     * @return array
     */
    public function getResponse(string $responseType, ?string $message="" , mixed $data=[]): JsonResponse
    {
        if($responseType===self::RESPONSE_TYPE_OK){
            return $this->ok($message,$data);
        }
        else if($responseType===self::RESPONSE_TYPE_CONFLICT){
            return $this->conflict($data);
        }
        else if ($responseType===self::RESPONSE_TYPE_NOT_VALID){
            return $this->notValid();
        }
        else if ($responseType===self::RESPONSE_TYPE_NOT_FOUND){
            return $this->notFound($message);
        }
        return [];
    }
}
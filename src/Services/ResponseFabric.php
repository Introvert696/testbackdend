<?php

namespace App\Services;

class ResponseFabric
{
    public const RESPONSE_TYPE_OK = "Ok";
    public const RESPONSE_TYPE_NOT_VALID = "Not valid";
    public const RESPONSE_TYPE_NOT_FOUND = "Not found";
    public const RESPONSE_TYPE_CONFLICT = "Conflict";
    public function __construct(
        private readonly ResponseHelper $responseHelper
    ){}
    private function notFound(string $message): array
    {
        return $this->responseHelper->generate(
            self::RESPONSE_TYPE_NOT_FOUND,
            $this->responseHelper::STATUS_NOT_FOUND,
            $message);
    }

    private function ok(string $message, mixed $data = []): array
    {
        return $this->responseHelper->generate(
            self::RESPONSE_TYPE_OK,
            $this->responseHelper::STATUS_OK,
            $message,
            $data ?? []);
    }

    private function notValid(): array
    {
        return $this->responseHelper->generate(
            self::RESPONSE_TYPE_NOT_VALID,
            $this->responseHelper::STATUS_NOT_VALID_FIELDS,
            'Check body');
    }

    private function conflict(mixed $data): array
    {
        return $this->responseHelper->generate(
            self::RESPONSE_TYPE_CONFLICT,
            $this->responseHelper::STATUS_CONFLICT,
            'Check fields',
            $data);
    }

    /**
     * @param string $responseType
     * @param string|null $message
     * @param mixed $data
     * @return array
     */
    public function getResponse(string $responseType, ?string $message="" , mixed $data=[]): array
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
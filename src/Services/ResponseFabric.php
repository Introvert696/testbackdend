<?php

namespace App\Services;

use App\Exception\ApiResponseException;

class ResponseFabric
{
    public function __construct(
        private readonly ResponseHelper $responseHelper
    ){}

    public function notFound(string $message): array
    {
        throw new ApiResponseException($message,code:$this->responseHelper::STATUS_NOT_FOUND,type:'Not found');
    }
    public function ok(string $message,mixed $data=null): array
    {
        return $this->responseHelper->generate(
            'Ok',
            $this->responseHelper::STATUS_OK,
            $message,
            $data??[]);
    }
    public function notValid(): array
    {
        throw new ApiResponseException('Check body',code:$this->responseHelper::STATUS_NOT_VALID_FIELDS,type:'Error');
    }
    public function conflict(mixed $data): array
    {
        throw new ApiResponseException('Check fields',code:$this->responseHelper::STATUS_CONFLICT,type:'Conflict',data:$data);

    }
}
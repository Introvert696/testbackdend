<?php

namespace App\Services;

class ResponseFabric
{
    public function __construct(
        private readonly ResponseHelper $responseHelper
    ){}

    public function notFound(string $message): array
    {
        return $this->responseHelper->generate(
            'Not found',
            $this->responseHelper::STATUS_NOT_FOUND,
            $message);
    }
    public function ok(string $message,mixed $data=null): array
    {
        return $this->responseHelper->generate(
            'Ok',
            $this->responseHelper::STATUS_OK,
            $message,
            $data);
    }
    public function notValid(): array
    {
        return $this->responseHelper->generate(
            'Error',
            $this->responseHelper::STATUS_NOT_VALID_FIELDS,
            'Check body');
    }
    public function conflict(mixed $data): array
    {
        return $this->responseHelper->generate(
            'Conflict',
            $this->responseHelper::STATUS_CONFLICT,
            'title has exists',
            $data);
    }
}
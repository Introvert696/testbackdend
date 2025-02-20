<?php

namespace App\Services;

use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;


class ResponseHelper
{
    public const STATUS_OK = 200;
    public const STATUS_NOT_VALID_FIELDS = 402;
    public const STATUS_NOT_VALID_BODY = 502;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_CONFLICT = 409;


    public function __construct(
        private readonly SerializerInterface $serializer,
    ){}

    public function generate(
        string $type,
        int $statusCode,
        string $message,
        array|object|null $data = null
    ): array
    {
        $response['type'] = $type;
        $response['code'] = $statusCode;
        $response['message'] = $message;
        if($data != null){
            $response['data'] = $data;
        }
        return $response;
    }
    public function first(array $data): object|null
    {
        if(empty($data)){
            return null;
        }
        return $data[0];
    }
    public function checkData($data,$class): object|array|null
    {
        try {
            $data = $this->serializer->deserialize($data,$class,'json');
        } catch (NotEncodableValueException|NotNormalizableValueException){
            return null;
        }

        return $data;
    }
}
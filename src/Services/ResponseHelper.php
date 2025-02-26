<?php

namespace App\Services;

use App\Exception\ApiResponseException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ResponseHelper
{
    public const STATUS_OK = 200;
    public const STATUS_NOT_VALID_FIELDS = 402;
    public const STATUS_NOT_FOUND = 404;
    public const STATUS_CONFLICT = 409;


    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator
    ){}

    public function generate(
        string $type,
        int $statusCode,
        string $message,
        array|object|false $data = false
    ): array
    {
        $response['type'] = $type;
        $response['code'] = $statusCode;
        $response['message'] = $message;
        if($data !== false){
            $response['data'] = $data;
        }
        return $response;
    }
    public function first(array $data): object|false
    {
        if(empty($data)){
            return false;
        }
        return $data[0];
    }
    public function checkRequest($data, $class): object|array|bool
    {
        try {
            $data = $this->serializer->deserialize($data,$class,'json');
            $response = $this->validator->validate($data);
            if(count($response)>0){
                throw new ApiResponseException('Check body',code:self::STATUS_NOT_VALID_FIELDS,type:'Error');
            }
        } catch (NotEncodableValueException|NotNormalizableValueException){
            return false;
        }

        return $data;
    }
}
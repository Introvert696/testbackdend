<?php

namespace App\Services;

use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;


class JsonResponseHelper
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ){}
    public function generate(string $type,int $statusCode,string $message,array|object|null $data = null): array{
        $response['type'] = $type;
        $response['code'] = $statusCode;
        $response['message'] = $message;
        if($data != null){
            $response['data'] = $data;
        }
        return $response;
    }
    public function first(array $data): object
    {
        return $data[0];
    }
    public function checkData($data,$class): object|array|null
    {
        try{
            $data = $this->serializer->deserialize($data,$class,'json');
        }
        catch (NotEncodableValueException){
            return null;
        }

        return $data;
    }
}
<?php

namespace App\Services;

class JsonResponseHelper
{
    /**
     * @param string $type response type
     * @param int $statusCode status code
     * @param string $message
     * @param array $data   some data
     * @return array
     */
    public function generate(string $type,int $statusCode,string $message,array $data = null): array{
        $response['type'] = $type;
        $response['code'] = $statusCode;
        $response['message'] = $message;
        if($data != null){
            $response['data'] = $data;
        }
        return $response;
    }
}
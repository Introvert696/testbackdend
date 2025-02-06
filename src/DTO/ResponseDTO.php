<?php

namespace App\DTO;

use App\Entity\Patients;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ResponseDTO
{
    #[OA\Property(type: 'string')]
    public string $type;
    #[OA\Property(type: 'integer')]
    public int $code;
    #[OA\Property(type: 'string')]
    public string $message;

    public object|array $data;

    public function getType(): string
    {
        return $this->type;
    }
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
    public function getCode(): int
    {
        return $this->code;
    }
    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
    public function getData(): object|array
    {
        return $this->data;
    }
    public function setData(object|array $data): static
    {
        $this->data = $data;

        return $this;
    }


}
<?php
namespace App\Exception;

class ApiResponseException extends \Exception
{
    private string $type;
    private array $data;

    public function __construct(
        string $message,
        int $code = 500,
        string $type = 'error',
        mixed $data = []
        )
    {
        $this->type = $type;
        $this->data = $data;
        parent::__construct($message, $code);
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getData(): mixed
    {
        return $this->data;
    }
}
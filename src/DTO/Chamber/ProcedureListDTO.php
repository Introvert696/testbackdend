<?php

namespace App\DTO\Chamber;


use Symfony\Component\Validator\Constraints as Assert;

class ProcedureListDTO
{

    #[Assert\NotBlank]
    public ?int $procedure_id = null;
    #[Assert\NotBlank]
    public ?int $queue = null;
    #[Assert\Type('bool')]
    public ?bool $status = null;
    public ?string $source_type = null;
    public ?int $source_id = null;

    public function getProclistId(): ?int
    {
        return $this->id;
    }

    public function setProclistId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getProcedureId(): ?int
    {
        return $this->procedure_id;
    }

    public function setProcedureId(?int $procedure_id): static
    {
        $this->procedure_id = $procedure_id;

        return $this;
    }

    public function getQueue(): ?int
    {
        return $this->queue;
    }

    public function setQueue(?int $queue): static
    {
        $this->queue = $queue;

        return $this;
    }

    public function getSourceType(): ?string
    {
        return $this->source_type;
    }

    public function setSourceType(?string $source_type): static
    {
        $this->source_type = $source_type;

        return $this;
    }

    public function getSourceId(): ?int
    {
        return $this->source_id;
    }

    public function setSourceId(?int $source_id): static
    {
        $this->source_id = $source_id;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): static
    {
        $this->status = $status;

        return $this;
    }


}
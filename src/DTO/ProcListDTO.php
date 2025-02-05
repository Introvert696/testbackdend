<?php

namespace App\DTO;


use Symfony\Component\Validator\Constraints as Assert;

class ProcListDTO
{

public ?int $id=null;

public ?int $procedure_id=null;
#[Assert\NotBlank]
public ?int $queue=null;

public ?bool $status=null;

public ?string $source_type=null;

public ?int $source_id=null;

    /**
     * @return int|null
     */
    public function getProclistId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $proclist_id
     */
    public function setProclistId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getProcedureId(): ?int
    {
        return $this->procedure_id;
    }

    /**
     * @param int|null $procedure_id
     */
    public function setProcedureId(?int $procedure_id): static
    {
        $this->procedure_id = $procedure_id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQueue(): ?int
    {
        return $this->queue;
    }

    /**
     * @param int|null $queue
     */
    public function setQueue(?int $queue): static
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSourceType(): ?string
    {
        return $this->source_type;
    }

    /**
     * @param string|null $source_type
     */
    public function setSourceType(?string $source_type): static
    {
        $this->source_type = $source_type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSourceId(): ?int
    {
        return $this->source_id;
    }

    /**
     * @param int|null $source_id
     */
    public function setSourceId(?int $source_id): static
    {
        $this->source_id = $source_id;

        return $this;
    }
    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool|null $status
     */
    public function setStatus(?bool $status): static
    {
        $this->status = $status;

        return $this;
    }


}
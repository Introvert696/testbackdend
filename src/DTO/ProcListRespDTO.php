<?php

namespace App\DTO;

class ProcListRespDTO
{
    public int $queue;
    public bool $status;
    public int $source_id;
    public string $source_type;

    public function getQueue(): int
    {
        return $this->queue;
    }
    public function setQueue(int $queue): static
    {
        $this->queue = $queue;

        return $this;
    }
    public function isStatus(): bool
    {
        return $this->status;
    }
    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }
    public function getSourceId(): int
    {
        return $this->source_id;
    }
    public function setSourceId(int $source_id): static
    {
        $this->source_id = $source_id;

        return $this;
    }
    public function getSourceType(): string
    {
        return $this->source_type;
    }
    public function setSourceType(string $sourceType): static
    {
        $this->source_type = $sourceType;

        return $this;
    }


}
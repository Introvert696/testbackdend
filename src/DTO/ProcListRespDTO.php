<?php

namespace App\DTO;

class ProcListRespDTO
{
    public int $queue;
    public bool $status;
    public int $sourceId;
    public string $sourceType;

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
        return $this->sourceId;
    }
    public function setSourceId(int $sourceId): static
    {
        $this->sourceId = $sourceId;

        return $this;
    }
    public function getSourceType(): string
    {
        return $this->sourceType;
    }
    public function setSourceType(string $sourceType): static
    {
        $this->sourceType = $sourceType;

        return $this;
    }


}
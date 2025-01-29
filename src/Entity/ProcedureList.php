<?php

namespace App\Entity;

use App\Repository\ProcedureListRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: ProcedureListRepository::class)]
class ProcedureList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'procedureLists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Procedures $procedures = null;

    #[ORM\Column(nullable: true)]
    private ?int $queue = null;

    #[ORM\Column(nullable: true)]
    private ?string $source_id;

    #[ORM\Column(nullable: true)]
    private ?string $source_type;

    #[ORM\Column]
    private ?bool $status = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProcedures(): ?Procedures
    {
        return $this->procedures;
    }

    public function setProcedures(?Procedures $procedures): static
    {
        $this->procedures = $procedures;

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

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }


    public function getSourceId(): ?string
    {
        return $this->source_id;
    }


    public function setSourceId(?string $source_id): static
    {
        $this->source_id = $source_id;

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

}

<?php

namespace App\Entity;

use App\Repository\ProceduresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: ProceduresRepository::class)]
class Procedures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, ProcedureList>
     */
    #[Ignore]
    #[ORM\OneToMany(targetEntity: ProcedureList::class, mappedBy: 'procedures',cascade: ['persist','remove'])]
    private Collection $procedureLists;

    public function __construct()
    {
        $this->procedureLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ProcedureList>
     */
    public function getProcedureLists(): Collection
    {
        return $this->procedureLists;
    }

    public function addProcedureList(ProcedureList $procedureList): static
    {
        if (!$this->procedureLists->contains($procedureList)) {
            $this->procedureLists->add($procedureList);
            $procedureList->setProcedures($this);
        }

        return $this;
    }

    public function removeProcedureList(ProcedureList $procedureList): static
    {
        if ($this->procedureLists->removeElement($procedureList)) {
            // set the owning side to null (unless already changed)
            if ($procedureList->getProcedures() === $this) {
                $procedureList->setProcedures(null);
            }
        }

        return $this;
    }
}

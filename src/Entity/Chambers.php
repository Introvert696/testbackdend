<?php

namespace App\Entity;

use App\Repository\ChambersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChambersRepository::class)]
class Chambers
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    #[OA\Property(description: 'Unique identifier')]
    #[Assert\Type('integer')]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    #[OA\Property(type: 'integer')]
    #[Assert\Type('integer')]
    private ?int $number = null;

    #[Ignore]
    #[ORM\OneToMany(targetEntity: ChambersPatients::class, mappedBy: 'chambers',cascade: ['persist','remove'])]
    #[OA\Property(ref: new Model(type: ChambersPatients::class))]
    private Collection $chambersPatients;

    public function __construct()
    {
        $this->chambersPatients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection<int, ChambersPatients>
     */
    public function getChambersPatients(): Collection
    {
        return $this->chambersPatients;
    }

    public function addChambersPatient(ChambersPatients $chambersPatient): static
    {
        if (!$this->chambersPatients->contains($chambersPatient)) {
            $this->chambersPatients->add($chambersPatient);
            $chambersPatient->setChambers($this);
        }

        return $this;
    }

    public function removeChambersPatient(ChambersPatients $chambersPatient): static
    {
        if ($this->chambersPatients->removeElement($chambersPatient)) {
            // set the owning side to null (unless already changed)
            if ($chambersPatient->getChambers() === $this) {
                $chambersPatient->setChambers(null);
            }
        }

        return $this;
    }
}

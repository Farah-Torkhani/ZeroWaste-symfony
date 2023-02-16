<?php

namespace App\Entity;

use App\Repository\CollecteCategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollecteCategorieRepository::class)]
class CollecteCategorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomCollectCat = null;

    #[ORM\Column]
    private ?int $pointCategorie = null;

    #[ORM\OneToMany(mappedBy: 'collecteCategorie', targetEntity: collecte::class)]
    private Collection $collecte_id;

    public function __construct()
    {
        $this->collecte_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCollectCat(): ?string
    {
        return $this->nomCollectCat;
    }

    public function setNomCollectCat(string $nomCollectCat): self
    {
        $this->nomCollectCat = $nomCollectCat;

        return $this;
    }

    public function getPointCategorie(): ?int
    {
        return $this->pointCategorie;
    }

    public function setPointCategorie(int $pointCategorie): self
    {
        $this->pointCategorie = $pointCategorie;

        return $this;
    }

    /**
     * @return Collection<int, collecte>
     */
    public function getCollecteId(): Collection
    {
        return $this->collecte_id;
    }

    public function addCollecteId(collecte $collecteId): self
    {
        if (!$this->collecte_id->contains($collecteId)) {
            $this->collecte_id->add($collecteId);
            $collecteId->setCollecteCategorie($this);
        }

        return $this;
    }

    public function removeCollecteId(collecte $collecteId): self
    {
        if ($this->collecte_id->removeElement($collecteId)) {
            // set the owning side to null (unless already changed)
            if ($collecteId->getCollecteCategorie() === $this) {
                $collecteId->setCollecteCategorie(null);
            }
        }

        return $this;
    }
}

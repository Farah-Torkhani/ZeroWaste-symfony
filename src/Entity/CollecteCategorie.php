<?php

namespace App\Entity;

use App\Repository\CollecteCategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: CollecteCategorieRepository::class)]
#[UniqueEntity(fields: ['nomCollectCat'], message: 'Ce nom de catégorie est déjà utilisé.')]
class CollecteCategorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"le champ est vide") ]  
    private ?string $nomCollectCat = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Assert\NotBlank(message:"le champ est vide") ]  
    private ?int $pointCategorie = null;

    #[ORM\OneToMany(mappedBy: 'collecteCategorie', targetEntity: Collecte::class)]
    private Collection $collect_id;

    public function __construct()
    {
        $this->collect_id = new ArrayCollection();
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
     * @return Collection<int, Collecte>
     */
    public function getCollectId(): Collection
    {
        return $this->collect_id;
    }

    public function addCollectId(Collecte $collectId): self
    {
        if (!$this->collect_id->contains($collectId)) {
            $this->collect_id->add($collectId);
            $collectId->setCollecteCategorie($this);
        }

        return $this;
    }

    public function removeCollectId(Collecte $collectId): self
    {
        if ($this->collect_id->removeElement($collectId)) {
            // set the owning side to null (unless already changed)
            if ($collectId->getCollecteCategorie() === $this) {
                $collectId->setCollecteCategorie(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getNomCollectCat();
    }
}

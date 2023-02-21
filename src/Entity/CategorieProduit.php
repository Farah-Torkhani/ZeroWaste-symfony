<?php

namespace App\Entity;

use App\Repository\CategorieProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieProduitRepository::class)]
class CategorieProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le champ est vide")]
    private ?string $nomCategorie = null;

    #[ORM\Column(length: 255)]
    private ?string $imageCategorie = null;

    #[ORM\OneToMany(mappedBy: 'categorieProduit', targetEntity: Produit::class)]
    private Collection $produit_id;

    public function __construct()
    {
        $this->produit_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): self
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    public function getImageCategorie(): ?string
    {
        return $this->imageCategorie;
    }

    public function setImageCategorie(string $imageCategorie): self
    {
        $this->imageCategorie = $imageCategorie;

        return $this;
    }

    /**
     * @return Collection<int, produit>
     */
    public function getProduitId(): Collection
    {
        return $this->produit_id;
    }

    public function addProduitId(produit $produitId): self
    {
        if (!$this->produit_id->contains($produitId)) {
            $this->produit_id->add($produitId);
            $produitId->setCategorieProduit($this);
        }

        return $this;
    }

    public function removeProduitId(produit $produitId): self
    {
        if ($this->produit_id->removeElement($produitId)) {
            // set the owning side to null (unless already changed)
            if ($produitId->getCategorieProduit() === $this) {
                $produitId->setCategorieProduit(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getNomCategorie();
    }
}

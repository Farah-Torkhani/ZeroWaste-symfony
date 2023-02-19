<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateAjout = null;

    #[ORM\ManyToOne(inversedBy: 'commande_id')]
    private ?Achat $achat = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $user_id = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Produit::class)]
    private Collection $produit_id;

    public function __construct()
    {
        $this->produit_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): self
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getAchat(): ?Achat
    {
        return $this->achat;
    }

    public function setAchat(?Achat $achat): self
    {
        $this->achat = $achat;

        return $this;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(?user $user_id): self
    {
        $this->user_id = $user_id;

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
            $produitId->setCommande($this);
        }

        return $this;
    }

    public function removeProduitId(produit $produitId): self
    {
        if ($this->produit_id->removeElement($produitId)) {
            // set the owning side to null (unless already changed)
            if ($produitId->getCommande() === $this) {
                $produitId->setCommande(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("produit_group")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le champ est vide")]
    #[Groups("produit_group")]
    private ?string $nomProduit = null;


    #[ORM\Column]
    #[Assert\NotBlank(message: "le champ est vide")]
    #[Assert\Positive]

    private ?int $quantite = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "le champ est vide")]
    #[Assert\Positive]
    #[Groups("produit_group")]
    private ?float $prixProduit = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("produit_group")]
    private ?string $Image = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "le champ est vide")]
    #[Groups("produit_group")]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'produit_id')]
    #[Groups("products")]
    private ?CategorieProduit $categorieProduit = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    #[Assert\NotBlank(message: "le champ est vide")]
    #[Groups("produit_group")]
    private ?int $prix_point_produit = null;

    #[ORM\Column(nullable: true)]
    private ?float $remise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etiquette = null;

    #[ORM\Column(nullable: true)]
    private ?float $score = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Notification::class)]
    private Collection $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): self
    {
        $this->nomProduit = $nomProduit;

        return $this;
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

    public function getPrixProduit(): ?float
    {
        return $this->prixProduit;
    }

    public function setPrixProduit(float $prixProduit): self
    {
        $this->prixProduit = $prixProduit;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategorieProduit(): ?CategorieProduit
    {
        return $this->categorieProduit;
    }

    public function setCategorieProduit(?CategorieProduit $categorieProduit): self
    {
        $this->categorieProduit = $categorieProduit;

        return $this;
    }

    public function getPrixPointProduit(): ?int
    {
        return $this->prix_point_produit;
    }

    public function setPrixPointProduit(?int $prix_point_produit): self
    {
        $this->prix_point_produit = $prix_point_produit;

        return $this;
    }

    public function getRemise(): ?float
    {
        return $this->remise;
    }

    public function setRemise(?float $remise): self
    {
        $this->remise = $remise;

        return $this;
    }

    public function getEtiquette(): ?string
    {
        return $this->etiquette;
    }

    public function setEtiquette(?string $etiquette): self
    {
        $this->etiquette = $etiquette;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setProduct($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getProduct() === $this) {
                $notification->setProduct(null);
            }
        }

        return $this;
    }
}

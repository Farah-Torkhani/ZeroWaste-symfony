<?php

namespace App\Entity;

use App\Repository\AchatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchatRepository::class)]
class Achat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_achat = null;

    #[ORM\OneToMany(mappedBy: 'achat', targetEntity: commande::class)]
    private Collection $commande_id;

    public function __construct()
    {
        $this->commande_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->date_achat;
    }

    public function setDateAchat(\DateTimeInterface $date_achat): self
    {
        $this->date_achat = $date_achat;

        return $this;
    }

    /**
     * @return Collection<int, commande>
     */
    public function getCommandeId(): Collection
    {
        return $this->commande_id;
    }

    public function addCommandeId(commande $commandeId): self
    {
        if (!$this->commande_id->contains($commandeId)) {
            $this->commande_id->add($commandeId);
            $commandeId->setAchat($this);
        }

        return $this;
    }

    public function removeCommandeId(commande $commandeId): self
    {
        if ($this->commande_id->removeElement($commandeId)) {
            // set the owning side to null (unless already changed)
            if ($commandeId->getAchat() === $this) {
                $commandeId->setAchat(null);
            }
        }

        return $this;
    }
}

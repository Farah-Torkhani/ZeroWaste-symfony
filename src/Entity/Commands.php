<?php

namespace App\Entity;

use App\Repository\CommandsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommandsRepository::class)]
class Commands
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("commands")]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups("commands")]
    private ?int $status = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: CommandsProduit::class,  cascade: ['persist', 'remove'])]
    private Collection $commandsProduits;

    #[ORM\ManyToOne]
    private ?User $user = null;

    public function __construct()
    {
        $this->commandsProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, CommandsProduit>
     */
    public function getCommandsProduits(): Collection
    {
        return $this->commandsProduits;
    }

    public function addCommandsProduit(CommandsProduit $commandsProduit): self
    {
        if (!$this->commandsProduits->contains($commandsProduit)) {
            $this->commandsProduits->add($commandsProduit);
            $commandsProduit->setCommande($this);
        }

        return $this;
    }

    public function removeCommandsProduit(CommandsProduit $commandsProduit): self
    {
        if ($this->commandsProduits->removeElement($commandsProduit)) {
            // set the owning side to null (unless already changed)
            if ($commandsProduit->getCommande() === $this) {
                $commandsProduit->setCommande(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'status' => $this->status,
        );
    }
}

<?php

namespace App\Entity;

use App\Repository\DonHistoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonHistoryRepository::class)]
class DonHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?float $donation_price = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_donation = null;

    #[ORM\OneToMany(mappedBy: 'donHistory', targetEntity: Fundrising::class)]
    private Collection $fundrisings_id;

    #[ORM\OneToMany(mappedBy: 'donHistory', targetEntity: User::class)]
    private Collection $user_don_id;

    public function __construct()
    {
        $this->fundrisings_id = new ArrayCollection();
        $this->user_don_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDonationPrice(): ?float
    {
        return $this->donation_price;
    }

    public function setDonationPrice(float $donation_price): self
    {
        $this->donation_price = $donation_price;

        return $this;
    }

    public function getDateDonation(): ?\DateTimeInterface
    {
        return $this->date_donation;
    }

    public function setDateDonation(\DateTimeInterface $date_donation): self
    {
        $this->date_donation = $date_donation;

        return $this;
    }

    /**
     * @return Collection<int, Fundrising>
     */
    public function getFundrisingsId(): Collection
    {
        return $this->fundrisings_id;
    }

    public function addFundrisingsId(Fundrising $fundrisingsId): self
    {
        if (!$this->fundrisings_id->contains($fundrisingsId)) {
            $this->fundrisings_id->add($fundrisingsId);
            $fundrisingsId->setDonHistory($this);
        }

        return $this;
    }

    public function removeFundrisingsId(Fundrising $fundrisingsId): self
    {
        if ($this->fundrisings_id->removeElement($fundrisingsId)) {
            // set the owning side to null (unless already changed)
            if ($fundrisingsId->getDonHistory() === $this) {
                $fundrisingsId->setDonHistory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserDonId(): Collection
    {
        return $this->user_don_id;
    }

    public function addUserDonId(User $userDonId): self
    {
        if (!$this->user_don_id->contains($userDonId)) {
            $this->user_don_id->add($userDonId);
            $userDonId->setDonHistory($this);
        }

        return $this;
    }

    public function removeUserDonId(User $userDonId): self
    {
        if ($this->user_don_id->removeElement($userDonId)) {
            // set the owning side to null (unless already changed)
            if ($userDonId->getDonHistory() === $this) {
                $userDonId->setDonHistory(null);
            }
        }

        return $this;
    }
}

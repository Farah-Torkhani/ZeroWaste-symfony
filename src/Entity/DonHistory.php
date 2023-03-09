<?php

namespace App\Entity;

use App\Repository\DonHistoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: DonHistoryRepository::class)]
class DonHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
   
    private ?int $id = null;
    #[Assert\NotBlank(message:"comment is required")]
    #[ORM\Column(length: 255)]
    private ?string $comment = null;
    #[Assert\NotBlank(message:"donation price is required")]
    #[ORM\Column]
    private ?float $donation_price = null;
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_donation = null;

    #[ORM\ManyToOne(inversedBy: 'donHistories')]
    private ?User $userID = null;

    #[ORM\ManyToOne(inversedBy: 'donHistories')]
    private ?Fundrising $fundsID = null;



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

    public function getUserID(): ?User
    {
        return $this->userID;
    }

    public function setUserID(?User $userID): self
    {
        $this->userID = $userID;

        return $this;
    }

    public function getFundsID(): ?Fundrising
    {
        return $this->fundsID;
    }

    public function setFundsID(?Fundrising $fundsID): self
    {
        $this->fundsID = $fundsID;

        return $this;
    }

    

}

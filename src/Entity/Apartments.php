<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ApartmentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={
 *          "get" = {"security" = "is_granted('ROLE_SUPERVISOR')"},
 *     },
 *     normalizationContext={"groups"={"apartments:read"}},
 *     denormalizationContext={"groups"={"apartments:write"}}
 * )
 * @ORM\Entity(repositoryClass=ApartmentsRepository::class)
 */
class Apartments
{
    /**
     * @Groups({"apartments:read"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"users:read","users:write","apartments:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * @Groups({"apartments:read"})
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=MonthlyPayments::class, mappedBy="apartment", cascade={"persist", "remove"})
     */
    private $monthlyPayments;

    /**
     * @ORM\OneToMany(targetEntity=WaterConsumptions::class, mappedBy="apartment", cascade={"persist", "remove"})
     */
    private $waterConsumptions;

    /**
     * @Groups({"users:read","users:write","apartments:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $entry;

    /**
     * @Groups({"apartments:read"})
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="apartment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getMonthlyPayments(): ?MonthlyPayments
    {
        return $this->monthlyPayments;
    }

    public function setMonthlyPayments(MonthlyPayments $monthlyPayments): self
    {
        $this->monthlyPayments = $monthlyPayments;

        // set the owning side of the relation if necessary
        if ($monthlyPayments->getApartment() !== $this) {
            $monthlyPayments->setApartment($this);
        }

        return $this;
    }

    public function getWaterConsumptions(): ?WaterConsumptions
    {
        return $this->waterConsumptions;
    }

    public function setWaterConsumptions(WaterConsumptions $waterConsumptions): self
    {
        $this->waterConsumptions = $waterConsumptions;

        // set the owning side of the relation if necessary
        if ($waterConsumptions->getApartment() !== $this) {
            $waterConsumptions->setApartment($this);
        }

        return $this;
    }

    public function getEntry(): ?string
    {
        return $this->entry;
    }

    public function setEntry(string $entry): self
    {
        $this->entry = $entry;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }
}

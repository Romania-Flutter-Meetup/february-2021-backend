<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ApiResource(
 *     collectionOperations={
 *          "get",
 *          "post" = {
 *              "security"="is_granted('ROLE_ADMIN')"
 *          },
 *      },
 *      itemOperations={
 *          "get",
 *          "put" = {
 *              "security"="is_granted('ROLE_SUPERVISOR')",
 *              "validations_groups" = {"Default","create"}
 *          },
 *          "monthly_payments_add"={
 *              "route_name"="monthly_payments_add",
 *              "method"="POST",
 *              "normalization_context":{"groups":{"monthly:write"}},
 *              "openapi_context" = {
 *                  "responses" = {
 *                      "202" = {
 *                          "description" = "Return MonthlyPayments Object",
 *                      },
 *                      "400" = {
 *                          "description" = "Invalid input"
 *                      },
 *                  },
 *                  "requestBody"={
 *                     "content"={
 *                         "json"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "apartment"={
 *                                         "type"="integer"
 *                                     },
 *                                      "coldWater"={
 *                                         "type"="number"
 *                                     },
 *                                     "hotWater"={
 *                                         "type"="number"
 *                                     },
 *                                      "total"={
 *                                         "type"="number"
 *                                     },
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 },
 *                 "summary" = "Add monthly payment."
 *              },
 *          },
 *     },
 *     normalizationContext={"groups"={"monthly:read"}},
 *     denormalizationContext={"groups"={"monthly:write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\MonthlyPaymentsRepository")
 */
class MonthlyPayments
{
    /**
     * @Groups({"monthly:read","monthly:write"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"monthly:read","monthly:write"})
     * @ORM\ManyToOne(targetEntity=Apartments::class, inversedBy="monthlyPayments", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $apartment;

    /**
     * @Groups({"monthly:read","monthly:write"})
     * @ORM\Column(type="float", precision=6, scale=2)
     */
    private $coldWater;

    /**
     * @Groups({"monthly:read","monthly:write"})
     * @ORM\Column(type="float", precision=6, scale=2)
     */
    private $hotWater;

    /**
     * @Groups({"monthly:read","monthly:write"})
     * @ORM\Column(type="float", precision=6, scale=2)
     */
    private $total;

    /**
     * @Groups({"monthly:read"})
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Groups({"monthly:read","monthly:write"})
     * @ORM\Column(type="boolean")
     */
    private $status;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApartment(): ?Apartments
    {
        return $this->apartment;
    }

    public function setApartment(Apartments $apartment): self
    {
        $this->apartment = $apartment;

        return $this;
    }

    public function getColdWater(): ?float
    {
        return $this->coldWater;
    }

    public function setColdWater(float $coldWater): self
    {
        $this->coldWater = $coldWater;

        return $this;
    }

    public function getHotWater(): ?float
    {
        return $this->hotWater;
    }

    public function setHotWater(float $hotWater): self
    {
        $this->hotWater = $hotWater;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\WaterConsumptionsRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get",
 *          "post" = {
 *              "security"="is_granted('ROLE_USER')"
 *          },
 *      },
 *      itemOperations={
 *          "get",
 *          "water_consumption_add"={
 *              "route_name"="water_consumption_add",
 *              "method"="POST",
 *              "normalization_context":{"groups":{"water:write"}},
 *              "openapi_context" = {
 *                  "responses" = {
 *                      "202" = {
 *                          "description" = "Return WaterConsumption Object",
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
 *                                     "bathroomHot"={
 *                                         "type"="number"
 *                                     },
 *                                      "bathroomCold"={
 *                                         "type"="number"
 *                                     },
 *                                     "kitchenHot"={
 *                                         "type"="number"
 *                                     },
 *                                      "kitchenCold"={
 *                                         "type"="number"
 *                                     },
 *                                 },
 *                             }
 *                         }
 *                     }
 *                 },
 *                 "summary" = "Add water consumption."
 *              },
 *          },
 *     },
 *     normalizationContext={"groups"={"water:read"}},
 *     denormalizationContext={"groups"={"water:write"}}
 * )
 * @ORM\Entity(repositoryClass=WaterConsumptionsRepository::class)
 */
class WaterConsumptions
{
   /**
    *  @Groups({"water:read","water:write"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"water:read","water:write"})
     * @ORM\ManyToOne(targetEntity=Apartments::class, inversedBy="waterConsumptions", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $apartment;

    /**
     * @Groups({"water:read","water:write"})
     * @ORM\Column(type="float")
     */
    private $bathroomHot;

    /**
     * @Groups({"water:read","water:write"})
     * @ORM\Column(type="float")
     */
    private $bathroomCold;

    /**
     * @Groups({"water:read","water:write"})
     * @ORM\Column(type="float")
     */
    private $kitchenHot;

    /**
     * @Groups({"water:read","water:write"})
     * @ORM\Column(type="float")
     */
    private $kitchenCold;

    /**
     * @Groups({"water:read","water:write"})
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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

    public function getBathroomHot(): ?float
    {
        return $this->bathroomHot;
    }

    public function setBathroomHot(float $bathroomHot): self
    {
        $this->bathroomHot = $bathroomHot;

        return $this;
    }

    public function getBathroomCold(): ?float
    {
        return $this->bathroomCold;
    }

    public function setBathroomCold(float $bathroomCold): self
    {
        $this->bathroomCold = $bathroomCold;

        return $this;
    }

    public function getKitchenHot(): ?float
    {
        return $this->kitchenHot;
    }

    public function setKitchenHot(float $kitchenHot): self
    {
        $this->kitchenHot = $kitchenHot;

        return $this;
    }

    public function getKitchenCold(): ?float
    {
        return $this->kitchenCold;
    }

    public function setKitchenCold(float $kitchenCold): self
    {
        $this->kitchenCold = $kitchenCold;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}

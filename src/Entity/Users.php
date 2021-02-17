<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "get" = {"security"="is_granted('ROLE_ADMIN')"},
 *          "post" = {"path"="users/register"}
 *      },
 *     itemOperations={
 *          "get" = {"security"="is_granted('ROLE_ADMIN') or object == user"},
 *          "put" = {
 *              "security"="is_granted('ROLE_ADMIN') or object == user",
 *              "validations_groups" = {"Default","create"}
 *          },
 *          "login"={
 *              "route_name"="api_login",
 *              "method" = "post",
 *              "openapi_context" = {
 *                 "requestBody"={
 *                     "content"={
 *                         "json"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "email"={
 *                                         "type"="string"
 *                                     },
 *                                      "password"={
 *                                         "type"="string"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 },
 *                  "responses" = {
 *                      "200" = {
 *                          "description" = "User logged in",
 *                          "schema" =  {
 *                              "type" = "object",
 *                              "required" = {
 *                                  "token",
 *                                  "refresh_token"
 *                              },
 *                              "properties" = {
 *                                   "token" = {
 *                                      "type" = "string"
 *                                   },
 *                                   "refresh_token" = {
 *                                      "type" = "string"
 *                                   }
 *                              }
 *                          }
 *                      },
 *                      "401" = {
 *                          "description" = "invalid password or email"
 *                      }
 *                  },
 *                  "summary" = "Login user in application",
 *                  "description" = "Login user with e-mail and password."
 *              }
 *          },
 *          "recover-password"={
 *              "route_name"="recover_password",
 *              "method" = "post",
 *              "openapi_context" = {
 *                 "requestBody"={
 *                     "content"={
 *                         "json"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "email"={
 *                                         "type"="string"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 },
 *                  "responses" = {
 *                      "204" = {
 *                          "description" = "E-mail sent for password recover"
 *                      },
 *                      "401" = {
 *                          "description" = "invalid email"
 *                      }
 *                  },
 *                  "summary" = "Recover username password",
 *                  "description" = "Recover username password"
 *              }
 *          },
 *     },
 *     normalizationContext={"groups"={"users:read"}},
 *     denormalizationContext={"groups"={"users:write"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={"email": "partial","name": "partial","phoneNumber": "partial"})
 * @ApiFilter(BooleanFilter::class, properties={"isActive","isSmsConfirmed","isEmailConfirmed"})
 * @ApiFilter(OrderFilter::class, properties={"id": "DESC"})
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @UniqueEntity(fields={"email"})
 */
class Users implements UserInterface
{
    /**
     * @Groups({"users:read"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"users:read","users:write"})
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Introduceți o adresă de e-mail")
     * @Assert\Email(message="Adresa de e-mail este invalidă")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"users:read"})
     *
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Groups({"users:write"})
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string The hashed password
     * @Groups({"users:write"})
     * @SerializedName("password")
     */
    private $plainPassword;

    /**
     * @Groups({"users:read","users:write"})
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Completați numele")
     */
    private $name;

    /**
     * @Groups({"users:read","users:write"})
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phoneNumber;

    /**
     * @Groups({"users:read"})
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"admin:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @Groups({"users:read"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isEmailConfirmed;

    /**
     * @Groups({"users:read","users:write"})
     * @ORM\OneToMany(targetEntity=Apartments::class, mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $apartment;

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERVISOR = 'ROLE_SUPERVISOR';
    const ROLE_USER = 'ROLE_USER';

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->isActive = false;
        $this->apartment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
//        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     *
     * @return $this
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getIsEmailConfirmed(): ?bool
    {
        return $this->isEmailConfirmed;
    }

    public function setIsEmailConfirmed(?bool $isEmailConfirmed): self
    {
        $this->isEmailConfirmed = $isEmailConfirmed;

        return $this;
    }

    /**
     * @return Collection|Apartments[]
     */
    public function getApartments(): Collection
    {
        return $this->apartment;
    }

    public function addApartment(Apartments $apartment): self
    {
        if (!$this->apartment->contains($apartment)) {
            $this->apartment[] = $apartment;
            $apartment->setUser($this);
        }

        return $this;
    }

    public function removeApartment(Apartments $apartment): self
    {
        if ($this->apartment->contains($apartment)) {
            $this->apartment->removeElement($apartment);
            // set the owning side to null (unless already changed)
            if ($apartment->getUser() === $this) {
                $apartment->setUser(null);
            }
        }

        return $this;
    }
}

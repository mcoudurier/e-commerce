<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints\PhoneNumber;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 * @ORM\Table(name="user_addresses")
 */
class Address
{
    const TYPE_BILLING = 'billing';
    const TYPE_SHIPPING = 'shipping';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="addresses")
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('address1', new Assert\Type('string'));
        $metadata->addPropertyConstraint('address1', new Assert\NotNull());

        $metadata->addPropertyConstraint('address2', new Assert\Type('string'));

        $metadata->addPropertyConstraint('city', new Assert\Type('string'));
        $metadata->addPropertyConstraint('city', new Assert\NotNull());

        $metadata->addPropertyConstraint('postCode', new Assert\Type('integer'));
        $metadata->addPropertyConstraint('postCode', new Assert\NotNull());

        $metadata->addPropertyConstraint('phone', new PhoneNumber([
            'message' => 'Numéro de téléphone invalide'
        ]));
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(?string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostCode(): ?int
    {
        return $this->postCode;
    }

    public function setPostCode(?int $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, [self::TYPE_BILLING, self::TYPE_SHIPPING])) {
            throw new \InvalidArgumentException('Invalid address type');
        }
        $this->type = $type;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
}

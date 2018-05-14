<?php
namespace App\Form\Model;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use App\Entity\Address;

class UserContact
{
    private $user;

    private $firstName;

    private $lastName;

    private $address;

    public function __construct(User $user, ?Address $address)
    {
        $this->user = $user;
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->address = $address;
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('firstName', new Assert\NotNull());
        $metadata->addPropertyConstraint('lastName', new Assert\NotNull());
        $metadata->addPropertyConstraint('address', new Assert\Valid());
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    
    public function setFirstName(?string $firstName)
    {
        $this->firstName = $firstName;
    }
    
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    
    public function setLastName(?string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address)
    {
        $this->address = $address;
    }
}

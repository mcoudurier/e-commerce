<?php
namespace App\Form\Model;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    private $oldPassword;

    private $newPassword;
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint(
            'oldPassword',
            new SecurityAssert\UserPassword([
                'message' => 'Mot de passe invalide'
            ])
        );
        $metadata->addPropertyConstraint('newPassword', new Assert\Length([
            'min' => 5,
            'max' => 50,
            'minMessage' => 'Le mot de passe doit faire au minimum 5 caractères',
            'maxMessage' => 'Le mot de passe doit faire au maximum 50 caractères'
        ]));
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }
    
    public function setOldPassword(string $oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }
    
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }
    
    public function setNewPassword(string $newPassword)
    {
        $this->newPassword = $newPassword;
    }
}

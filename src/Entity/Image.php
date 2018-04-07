<?php
namespace App\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @ORM\Table(name="images")
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="images")
     * @ORM\JoinColumn(name="productId", referencedColumnName="id")
     */
    private $productId;

    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @ORM\Column(type="string")
     */
    private $description;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $size;
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProductId()
    {
        return $this->productId;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function getSize(): int
    {
        return $this->size;
    }
    
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function setSize($size)
    {
        $this->size = $size;
    }
}

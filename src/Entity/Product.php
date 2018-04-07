<?php
namespace App\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="products")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
    private $category;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="decimal", scale=3)
     */
    private $weight;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="productId")
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('id', new Assert\GreaterThan(0));
        $metadata->addPropertyConstraint('name', new Assert\Type('string'));
        $metadata->addPropertyConstraint('description', new Assert\Type('string'));
        $metadata->addPropertyConstraint('category', new Assert\Type('string'));
        $metadata->addPropertyConstraint('stock', new Assert\Type('int'));
        $metadata->addPropertyConstraint('price', new Assert\Type('float'));
        $metadata->addPropertyConstraint('weight', new Assert\Type('float'));
        $metadata->addPropertyConstraint('images', new Assert\Type('array'));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function setImages($images)
    {
        $this->images = $images;
    }
}

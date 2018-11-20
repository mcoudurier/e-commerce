<?php
namespace App\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="images")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $product;

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

    /**
     * The uploaded file
     *
     * @var [UploadedFile]
     */
    private $file;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('file', new Assert\Image());

        $metadata->addPropertyConstraint('description', new Assert\Type('string'));
        $metadata->addPropertyConstraint('description', new Assert\NotNull());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function getSize(): int
    {
        return $this->size;
    }
    
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }
    
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    public function setDescription(string $description)
    {
        $this->description = $description;
    }
    
    public function setSize(int $size)
    {
        $this->size = $size;
    }
    
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }
}

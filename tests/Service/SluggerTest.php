<?php
namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\Slugger;
use App\Entity\Product;
use App\Repository\ProductRepository;

class SluggerTest extends TestCase
{
    private $productRepository;

    public function setUp()
    {
        $this->productRepository = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(['findDuplicateSlug'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testSlugify()
    {
        $product = new Product();
        $product->setId(1);
        $product->setName('pr~¹|\[|\o^&#duct$£+=)! name');
        $slugger = new Slugger($this->productRepository);

        $slug = $slugger->slugify($product);
        $this->assertEquals('product-name', $slug);
    }

    public function testSlugifyWithAccent()
    {
        $product = new Product();
        $product->setId(1);
        $product->setName('product name éèàçâäùü');
        $slugger = new Slugger($this->productRepository);

        $slug = $slugger->slugify($product);
        $this->assertEquals('product-name-eeacaauu', $slug);
    }

    public function testSlugifyAlreadyExists()
    {
        $product = new Product();
        $product->setId(1);
        $product->setName('product-name');
        
        $this->productRepository->expects($this->any())
            ->method('findDuplicateSlug')
            ->willReturn($product);

        $slugger = new Slugger($this->productRepository);
        $slug = $slugger->slugify($product);
        
        $this->assertEquals('product-name-1', $slug);
    }

    public function testSlugifyAlreadyExistsWithSuffix()
    {
        $product = new Product();
        $product->setId(1);
        $product->setName('product-name-1');
        
        $this->productRepository->expects($this->any())
            ->method('findDuplicateSlug')
            ->willReturn($product);

        $slugger = new Slugger($this->productRepository);
        $slug = $slugger->slugify($product);
        
        $this->assertEquals('product-name-2', $slug);
    }
}

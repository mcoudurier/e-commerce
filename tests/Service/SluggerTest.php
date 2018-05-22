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
        $newProduct = new Product();
        $newProduct->setId(1);
        $newProduct->setName('product name');

        $lastProduct = new Product();
        $lastProduct->setId(2);
        $lastProduct->setSlug('product-name');
        
        $this->productRepository->expects($this->any())
            ->method('findDuplicateSlug')
            ->willReturn($lastProduct);

        $slugger = new Slugger($this->productRepository);
        $slug = $slugger->slugify($newProduct);
        
        $this->assertEquals('product-name-1', $slug);
    }

    public function testSlugifyAlreadyExistsWithSuffix()
    {
        $newProduct = new Product();
        $newProduct->setId(1);
        $newProduct->setName('product name');
        
        $lastProduct = new Product();
        $lastProduct->setId(2);
        $lastProduct->setSlug('product-name-1');
        
        $this->productRepository->expects($this->any())
            ->method('findDuplicateSlug')
            ->willReturn($lastProduct);

        $slugger = new Slugger($this->productRepository);
        $slug = $slugger->slugify($newProduct);
        
        $this->assertEquals('product-name-2', $slug);
    }
}

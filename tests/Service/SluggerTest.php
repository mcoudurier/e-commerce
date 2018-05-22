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
            ->setMethods(['findOneBySlug'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testSlugify()
    {
        $string = 'pr~¹|\[|\o^&#duct$£+=)! name';
        $slugger = new Slugger($this->productRepository);

        $slug = $slugger->slugify($string);
        $this->assertEquals('product-name', $slug);
    }

    public function testSlugifyWithAccent()
    {
        $string = 'product name éèàçâäùü';
        $slugger = new Slugger($this->productRepository);

        $slug = $slugger->slugify($string);
        $this->assertEquals('product-name-eeacaauu', $slug);
    }

    public function testSlugifyAlreadyExists()
    {
        $product = (new Product())
            ->setSlug('product-name');
        
        $this->productRepository->expects($this->any())
            ->method('findOneBySlug')
            ->willReturn($product);

        $slugger = new Slugger($this->productRepository);
        $slug = $slugger->slugify($product->getSlug());
        
        $this->assertEquals('product-name-1', $slug);
    }

    public function testSlugifyAlreadyExistsWithSuffix()
    {
        $product = (new Product())
            ->setSlug('product-name-1');
        
        $this->productRepository->expects($this->any())
            ->method('findOneBySlug')
            ->willReturn($product);

        $slugger = new Slugger($this->productRepository);
        $slug = $slugger->slugify($product->getSlug());
        
        $this->assertEquals('product-name-2', $slug);
    }
}

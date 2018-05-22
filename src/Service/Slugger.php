<?php
namespace App\Service;

use App\Repository\ProductRepository;

class Slugger
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function slugify($product): string
    {
        $name = $product->getName();
        $slug = str_replace(' ', '-', $name);
        $transliterator = \Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;', \Transliterator::FORWARD);
        $slug = $transliterator->transliterate($slug);
        $slug = preg_replace('/[^a-z0-9\-]/i', '', $slug);

        $lastProduct = $this->productRepository->findDuplicateSlug($product->getId(), $slug);

        if ($lastProduct) {
            $lastSlug = $lastProduct->getSlug();
            $lastChar = mb_substr($lastSlug, -1);
            if (is_numeric($lastChar)) {
                $lastChar++;
                $slug .= '-' . $lastChar;
            } else {
                $slug .= '-1';
            }
        }

        return $slug;
    }
}

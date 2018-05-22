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

    public function slugify(string $name): string
    {
        $slug = str_replace(' ', '-', $name);
        $transliterator = \Transliterator::createFromRules(':: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;', \Transliterator::FORWARD);
        $slug = $transliterator->transliterate($slug);
        $slug = preg_replace('/[^a-z0-9\-]/i', '', $slug);

        $product = $this->productRepository->findOneBySlug($slug);

        if ($product) {
            $lastChar = substr($slug, -1);
            if (is_numeric($lastChar)) {
                $lastChar++;
                $slug = substr($slug, 0, -1) . $lastChar;
            } else {
                $slug = $slug . '-1';
            }
        }

        return $slug;
    }
}

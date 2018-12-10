<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductType;
use App\Entity\Product;
use App\Entity\Image;
use App\Service\Slugger;

class ProductController extends AbstractController
{
    public function index(Request $req, $page)
    {
        $form = $this->createFormBuilder()
            ->add('search', SearchType::class)
            ->getForm();
        
        $form->handleRequest($req);
            
        $maxResults = 10;
        $firstResult = $maxResults * ($page - 1);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->getData();
            
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->search($query['search'], $firstResult, $maxResults);
        } else {
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->getPaginated($firstResult, $maxResults);
        }
        
        $totalResults = count($products);
        $totalPages = 1;
        if ($totalResults > 0) {
            $totalPages = ceil($totalResults / $maxResults);
        }

        return $this->render('admin/all_products.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'total_pages' => $totalPages,
            'current_page' => $page,
        ]);
    }
    
    public function editor(Request $req, $id, Slugger $slugger)
    {
        $product = new Product();
        $title = 'Nouveau produit';
        
        if ($id) {
            $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->find($id);
            
            if (!$product) {
                throw $this->createNotFoundException('Ce produit n\'existe pas');
            }
            
            $title = 'Modification d\'un produit';
        } else {
            $product->addImage(new Image());
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($product->getImages() as $image) {
                if ($file = $image->getFile()) {
                    $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                    $filesize = filesize($file);
                    $image->setSize($filesize);
                    $image->setName($filename);
                    $file->move($this->getParameter('images_directory'), $filename);
                }
            }
            
            $slug = $slugger->slugify($product);
            $product->setSlug($slug);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            
            $this->addFlash('success', 'Produit ajouté');
            
            return $this->redirect($this->generateUrl('admin_product-editor', [
                'id' => $product->getId(),
            ]));
        }

        return $this->render('admin/product_editor.html.twig', [
            'form' => $form->createView(),
            'title' => $title,
        ]);
    }

    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
       
        $product = $em->getRepository(Product::class)->find($id);
        $product->setDeletedAt(new \Datetime());
        
        $em->persist($product);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé');

        return $this->redirectToRoute('admin_index');
    }
}

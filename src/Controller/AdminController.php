<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProductType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Product;

class AdminController extends Controller
{
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    public function productEditor(Request $req)
    {
        $product = new Product();
        $title = 'Nouveau produit';
        
        if ($id = $req->get('id'))
        {
            $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->find($id);
            
            if (!$product) {
                throw $this->createNotFoundException('Ce produit n\'existe pas');
            }
            
            $title = 'Modification d\'un produit';
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid())
        {
            foreach ($product->getImages() as $image)
            {
                $file = $image->getFile();
                $filename = time().'_'.$file->getClientOriginalName();
                $filesize = filesize($file);
                $image->setSize($filesize);
                $image->setName($filename);
                $file->move($this->getParameter('images_directory'), $filename);
            }

            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            
            $this->addFlash('success', 'Produit ajouté');
        }

        return $this->render('admin/product_editor.html.twig', [
            'form' => $form->createView(),
            'title' => $title
        ]);
    }

    public function allProducts(Request $req)
    {
        $form = $this->createFormBuilder()
            ->add('search', SearchType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->getForm();
        
        $form->handleRequest($req);
            
        $maxResults = 10;
        $currentPage = $req->get('page');
        $firstResult = $maxResults * ($currentPage - 1);
       
        if ($form->isSubmitted() && $form->isValid())
        {
            $query = $form->getData();
            
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->search($query['search'], $firstResult, $maxResults);
        }
        else
        {
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->getPaginated($firstResult, $maxResults);
        }
        
        $totalResults = count($products);
        $totalPages = ceil($totalResults / $maxResults);

        return $this->render('admin/all_products.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ]);
    }
}

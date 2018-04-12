<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Product;

class AdminController extends Controller
{
    public function index()
    {
        return $this->render('Admin/adminPanel.html.twig');
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

        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('category', TextType::class)
            ->add('stock', IntegerType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        return $this->render('Admin/productEditor.html.twig', [
            'form' => $form->createView(),
            'title' => $title
        ]);
    }
}

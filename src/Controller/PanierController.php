<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Service\Panier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(RequestStack $requestStack, Panier $panier)
    {
        $session = $requestStack->getSession();
        $monPanier = $session->get('panier');

        // dump($monPanier);
        
                $total = $panier->totalPanier();
        
                // dd($total);

        return $this->render('panier/panier.html.twig', [
            "monPanier" => $monPanier,
            "total" => $total
        ]);
    }

    #[Route('/panier/add', name: 'panier_add')]
    public function add_panier(Request $request, ProductRepository $productRepository, Panier $panier)
    {
        $quantity = $request->request->get('quantity');
        $id_product = $request->request->get('id');
        $product = $productRepository->find($id_product);
        
        $panier->add($product->getName(),$id_product,$quantity,$product->getPrixTTC());
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/delete/{id}', name: 'panier_delete', methods: "GET")]
    public function delete($id, Panier $panier)
    {
        $panier->remove($id);   
        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/delete', name: 'panier_delete_all', methods: "GET")]
    public function deleteAll(Panier $panier)
    {
        $panier->clear();   
        return $this->redirectToRoute('app_panier');
    }


    #[Route('/panier/validation', name:'panier_validate')]
    public function panierValidate(RequestStack $requestStack,Panier $panier, OrderRepository $orderRepository, OrderItemRepository $orderItemRepository){

        
        return $this->redirectToRoute('profil');


    }



}

<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\OrderItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session, ProductRepository $repoProduct): Response
    {
        $panier = $session->get('panier', []);

        $infoPanier = [];

        foreach ($panier as $id => $quantity) {
            $infoPanier[] = [
                'product' => $repoProduct->find($id),
                'quantity' => $quantity,
            ];
        }

        $total = 0;

        foreach ($infoPanier as $article) {
            $prixTotal = $article['product']->getPrixTTC() * $article['quantity'];
            $total += $prixTotal;
        }

        return $this->render('panier/panier.html.twig', [
            'articles' => $infoPanier,
            'total' => $total
        ]);
    }

    #[Route('/panier/add/{id}', name: 'panier_add', methods: "GET")]
    public function add($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }


        $session->set('panier', $panier);

        return $this->redirectToRoute('homepage');
    }

    #[Route('/panier/delete/{id}', name: 'panier_delete', methods: "GET")]
    public function delete(Product $product, SessionInterface $session,ProductRepository $repoProduct)
    {


        $panier = $session->get('panier', []);
        $id = $product->getId();

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id] --;
            } else {
                unset($panier[$id]);
            }
            
        } else {
            $panier[$id] = 1;
        }
        

        // dd($panier);
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/delete', name: 'panier_delete_all', methods: "GET")]
    public function deleteAll(SessionInterface $session)
    {
        $panier = $session->get('panier');
        $session->remove('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }


    #[Route("/panier/validate", name: "panier_validate")]
    public function panierValidate(SessionInterface $session, ProductRepository $productRepository, OrderItemRepository $orderItemRepository)
    {
        $panier = $session->get('panier');

        $infoPanier = [];

        $orderItems = new OrderItem;

        foreach ($panier as $id => $quantity) {
            $infoPanier[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity,
            ];

            dump($infoPanier);
    
            dump($orderItems);
            
            dd($panier);

            $orderItemRepository->add(new OrderItem);
        }

    }
}

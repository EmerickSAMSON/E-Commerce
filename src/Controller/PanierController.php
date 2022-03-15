<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(SessionInterface $session, ProductRepository $repoProduct): Response
    {
        $panier = $session->get('panier', []);

        $infoPanier = [];

        foreach($panier as $id =>$quantity) {
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

    #[Route('/panier/add/{id}', name: 'panier_add')]
    public function add($id, SessionInterface $session){
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id] ++;

        }else {
            $panier[$id] = 1;
        }


        $session->set('panier', $panier);

        dd($session->get('panier'));
    }
}

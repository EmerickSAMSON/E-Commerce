<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(ProductRepository $repoProduct): Response
    {

        $product = $repoProduct->findAll();

        return $this->render('homepage/homepage.html.twig', [
            'products' => $product
        ]);
    }


    #[Route('/profil', name: 'profil')]
    public function profil(){
        return $this->render('homepage/profil.html.twig',[
            
        ]);
    }
}

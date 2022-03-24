<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Panier
{

    private $requestStack;
    private $productRepository;
    private $orderRepository;
    private $orderItemRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository, OrderRepository $orderRepository, OrderItemRepository $orderItemRepository)
    {
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }


    public function creation_panier()
    {
        $panier = [
            'name' => [],
            'id_product' => [],
            'quantity' => [],
            'unit_Price' => [],
            'total_Price_HT' => [],
            'total_Price_TTC' => [],

        ];

        return $panier;
    }
    public function add($name, $id_product, $quantity, $unitPrice)
    {
        $product = $this->productRepository->find($id_product);
        $session = $this->requestStack->getSession();
        $panierSession = $session->get('panier');
        $totalPriceHT = $product->getPrixHT() * $quantity;
        $totalPriceTTC = ($product->getPrixTTC() * $quantity);
        if (empty($panierSession)) {
            $panierNew = $this->creation_panier();
            $session->set('panier', $panierNew);
            $panierSession = $session->get('panier');
        }
        $product_position = array_search($id_product, $panierSession["id_product"]);
        if ($product_position !== false) {
            $panierSession["quantity"][$product_position] += $quantity;
            $session->set('panier', $panierSession);
        } else {
            $panierSession["name"][] = $name;
            $panierSession["id_product"][] = $id_product;
            $panierSession["quantity"][] = $quantity;
            $panierSession["unit_Price"][] = $unitPrice;
            $panierSession["total_Price_HT"][] = $totalPriceHT;
            $panierSession["total_Price_TTC"][] = $totalPriceTTC;
            $session->set('panier', $panierSession);
        }
    }

    public function clear()
    {
        $this->requestStack->getSession()->remove('panier');
    }

    public function remove($id_product_delete){
        $session = $this->requestStack->getSession();
        $panierSession = $session->get('panier');
        $product_position = array_search($id_product_delete, $panierSession["id_product"]);
        array_splice($panierSession['name'], $product_position, 1);
        array_splice($panierSession['id_product'], $product_position, 1);
        array_splice($panierSession['quantity'], $product_position, 1);
        array_splice($panierSession['unit_Price'], $product_position, 1);
        array_splice($panierSession['total_Price_HT'], $product_position, 1);
        array_splice($panierSession['total_Price_TTC'], $product_position, 1);
        $session->set('panier', $panierSession);
    }

    public function totalPanier()
    {
        $totalPanier = 0;
        $session = $this->requestStack->getSession();
        $panierSession = $session->get('panier');
        if (!empty($panierSession)) {
            foreach ($panierSession["total_Price_TTC"] as $article) {
                $totalArticle = $article;
                $totalPanier += $totalArticle;
            }
            return $totalPanier;
        }
    }
}

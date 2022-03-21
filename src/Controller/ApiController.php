<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderItemRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{

    private $orderRepository;
    private $orderItemRepository;
    private $productRepository;

    public function __construct(OrderRepository $orderRepository, OrderItemRepository $orderItemRepository, ProductRepository $productRepository )
    {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository = $productRepository;
    }


    #[Route('/', name: 'api')]
    public function index()
    {

    }


    #[Route("/product/liste", name: "product_liste", methods:"GET")]
    public function Productlist(){
        $product = $this->productRepository->findAll();

        $encoder = [new JsonEncoder()];

        $normalizer = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizer, $encoder);

        $jsonContent = $serializer->serialize($product, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        dump($jsonContent);

        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        // dd($response);

        return $response;

    }

    #[Route("/order/liste", name: "order_liste", methods:"GET")]
    public function getAllOrders(){

        $order = $this->orderRepository->findAll();

        $encoder = [new JsonEncoder()];

        $normalizer = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizer, $encoder);

        $jsonContent = $serializer->serialize($order, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
            
        ]);

        
        $response = new Response($jsonContent);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
        }
}

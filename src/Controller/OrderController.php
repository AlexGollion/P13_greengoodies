<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Entity\Product;

final class OrderController extends AbstractController
{
    public function __construct(private OrderRepository $orderRepository, private Security $security,
        private ProductRepository $productRepository)
    {
    }
    #[Route('/order/panier', name: 'app_order_panier')]
    public function panier(): Response
    {
        $user = $this->security->getUser();
        $order = $this->orderRepository->findUserOrder($user->getId());
        $orderProducts = $order[0]->getOrderProducts();

        $products = array();
        foreach ($orderProducts as $orderProduct) {
            $productId = $orderProduct->getProductId();
            $product = $this->productRepository->find($productId);
            array_push($products, $product);
        }

        return $this->render('order/panier.html.twig', [
            'products' => $products,
            'orderProducts' => $orderProducts
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

final class OrderController extends AbstractController
{
    public function __construct(private OrderRepository $orderRepository, private Security $security,
        private ProductRepository $productRepository, private EntityManagerInterface $entityManager)
    {
    }
    #[Route('/order/panier', name: 'app_order_panier')]
    public function panier(): Response
    {
        $user = $this->security->getUser();
        $order = $this->orderRepository->findUserOrder($user->getId());
        // Si le panier n'est pas vide, on récupère tout les produits du panier
        if ($order != null) {
            $orderProducts = $order[0]->getOrderProducts();
            $products = array();
            foreach ($orderProducts as $orderProduct) {
                if ($orderProduct->getDeleteDate() == null) {
                    $productId = $orderProduct->getProductId();
                    $product = $this->productRepository->find($productId);
                    array_push($products, $product);
                }
            }
            $price = $order[0]->getPrice();
        }      
        // Si le panier est vide
        else 
        {
            $products = array();
            $orderProducts = array();
            $price = 0;
        }
        
        
        
        return $this->render('order/panier.html.twig', [
            'products' => $products,
            'orderProducts' => $orderProducts,
            'price' => $price
        ]);
    }

    /**
    * Route pour valider la commande
    **/
    #[Route('/order/validate', name: 'app_order_validate')]
    public function validateOrder(): Response 
    {
        $user = $this->security->getUser();
        $order = $this->orderRepository->findUserOrder($user->getId());
        if ($order != null) {
            $order[0]->setValidate(true);
            $order[0]->setDate(new \DateTimeImmutable());
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_order_compte');
    }

    #[Route('/order/compte', name: 'app_order_compte')]
    public function compte(): Response 
    {
        $user = $this->security->getUser();
        $orders = $this->orderRepository->findUserOrderValidate($user->getId());
        return $this->render('order/compte.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/order/activate', name: 'app_order_activate')]
    public function activate(): Response
    {
        $user = $this->security->getUser();
        
        if(!$user->isApiAccess()) {
            $user->setApiAccess(true);
            $this->entityManager->flush();
        }
        else
        {
            $user->setApiAccess(false);
            $this->entityManager->flush();
        }
        
        return $this->redirectToRoute('app_order_compte');
    }

    #[Route('/order/delete', name: 'app_order_delete')]
    public function deletePanier(): Response 
    {
        $user = $this->security->getUser();
        $order = $this->orderRepository->findUserOrder($user->getId());
        if ($order != null) {
            // On supprime le panier
            $order[0]->setDeleteDate(new \DateTimeImmutable());
            // On supprime les orderProducts
            foreach($order[0]->getOrderProducts() as $orderProduct) {
                $orderProduct->setDeleteDate(new \DateTimeImmutable());
            }
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_order_panier');
    }
}

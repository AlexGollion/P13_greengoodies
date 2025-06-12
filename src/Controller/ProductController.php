<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Form\OrderProductForm;

final class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository, private EntityManagerInterface $entityManager, 
        private OrderRepository $orderRepository, private Security $security){
    }
    #[Route('/product/{id}', name: 'app_product')]
    public function displayProduct($id, Request $request): Response
    {
        $product = $this->productRepository->find($id);
        $form = $this->createForm(OrderProductForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $order = $this->orderRepository->findUserOrder($user->getId());

            if ($order == null) {
                
                $order = new Order();
                $order->setUserId($user);
                $order->setValidate(false);
                $order->setPrice($product->getPrice());
                
                $orderProduct = new OrderProduct();
                $orderProduct->setQuantity($form->get('quantity')->getData());
                $orderProduct->setProductId($product);
                $orderProduct->setOrderId($order);
                $order->addOrderProduct($orderProduct);

                $this->entityManager->persist($order);
                $this->entityManager->persist($orderProduct);
                $this->entityManager->flush();
            }
            /*if ($form->get('quantity')->getData() !== 0) {
                $orderProduct = new OrderProduct();
                $orderProduct->setQuantity($form->get('quantity')->getData());
                $orderProduct->setProductId($product->getId());
                
                }*/
            return $this->redirectToRoute('app_order');
        }

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\OrderRepository;

final class OrderController extends AbstractController
{
    public function __construct(private OrderRepository $orderRepository, private Security $security)
    {
    }
    #[Route('/order', name: 'app_order')]
    public function order(): Response
    {
        $user = $this->security->getUser();
        $order = $this->orderRepository->findUserOrder($user->getId());

        return $this->render('order/index.html.twig', [
            'order' => $order,
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

final class MainController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository) {
    }
    #[Route('/', name: 'app_main')]
    public function home(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->render('home/home.html.twig', [
            'products' => $products
        ]);
    }
}

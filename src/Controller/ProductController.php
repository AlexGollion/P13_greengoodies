<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;

final class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository) {
    }
    #[Route('/product/{id}', name: 'app_product')]
    public function displayProduct($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        return $this->render('product/index.html.twig', [
            'product' => $product,
        ]);
    }
}

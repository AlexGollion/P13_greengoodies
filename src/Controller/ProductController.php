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
        $dataDisplay = $this->getUserProductInfo($id);
        $form = $this->createForm(OrderProductForm::class, null, [
            'quantity' => $dataDisplay[0],
        ]);
        

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $order = $this->orderRepository->findUserOrder($user->getId());

            // Si l'utilisateur n'a pas encore de panier et qu'il ajoute un produit (quantité supérieur à 0)
            if ($order == null && $form->get('quantity')->getData() !== 0) {
                // On crée une nouvelle commande
                $order = new Order();
                $order->setUserId($user);
                $order->setValidate(false);
                $order->setPrice($product->getPrice());
                // On crée un nouvel orderProduct
                $orderProduct = new OrderProduct();
                $orderProduct->setQuantity($form->get('quantity')->getData());
                $orderProduct->setProductId($product);
                $orderProduct->setOrderId($order);
                $order->addOrderProduct($orderProduct);
                $this->totalPrice($order);

                $this->entityManager->persist($order);
                $this->entityManager->persist($orderProduct);
                $this->entityManager->flush();

                return $this->redirectToRoute('app_order_panier');
            }
            // Si l'utilisateur a un panier et qu'il ajoute un produit (quantité supérieur à 0)
            else if ($order != null && $form->get('quantity')->getData() !== 0) {
                $orderProductList = $order[0]->getOrderProducts();
                $find = false;
                $i = 0;
                // On cherche si le produit est dans le panier et que il a pas été enlevé
                while ($i < $orderProductList->count() && !$find) {
                    if ($orderProductList[$i]->getProductId()->getId() == $id && $orderProductList[$i]->getDeleteDate() == null) {
                        // Si oui, on met à jour la quantité
                        $orderProductList[$i]->setQuantity($form->get('quantity')->getData());
                        $find = true;
                    }
                    $i++;
                }

                // Si le produit n'est pas dans le panier, on crée un nouvel orderProduct
                if (!$find) {
                    $orderProduct = new OrderProduct();
                    $orderProduct->setQuantity($form->get('quantity')->getData());
                    $orderProduct->setProductId($product);
                    $orderProduct->setOrderId($order[0]);
                    $order[0]->addOrderProduct($orderProduct);
                    $this->entityManager->persist($orderProduct);
                }
                $this->totalPrice($order[0]);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_order_panier');
            }
            // Si le panier n'est pas vide et qu'il retire un produit (quantité à 0)
            else if ($order != null && $form->get('quantity')->getData() == 0) {
                $orderProductList = $order[0]->getOrderProducts();
                $find = false;
                $i = 0;
                // On cherche si le produit est dans le panier
                while ($i < $orderProductList->count() && !$find) {
                    if ($orderProductList[$i]->getProductId()->getId() == $id && $orderProductList[$i]->getDeleteDate() == null) {
                        $date = new \DateTimeImmutable();
                        $orderProductList[$i]->setDeleteDate($date);
                        $find = true;
                    }
                    $i++;
                }
                $this->totalPrice($order[0]);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_order_panier');
            }
        }

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'form' => $form,
            'libelle' => $dataDisplay[1]
        ]);
    }

    /**
    * Calcule le prix total de la commande
    * @param Order $order: la commande en cours
    **/
    private function totalPrice(Order $order) {
        $total = 0;
        foreach ($order->getOrderProducts() as $orderProduct) {
            if ($orderProduct->getDeleteDate() == null) {
                $total += $orderProduct->getProductId()->getPrice() * $orderProduct->getQuantity();
            }
        }
        $order->setPrice($total);
    }

    /**
    * Retourne la quantité et le libelle de l'ajout au panier 
    * @param int $productId: l'id du produit
    **/ 
    private function getUserProductInfo($productId) : array
    {
        $user = $this->security->getUser();
        $result = array();
        // Si l'utilisateur est connecté alors on récupère son panier
        if ($user != null) {
            $order = $this->orderRepository->findUserOrder($user->getId());
            // Si le panier n'est pas vide, on cherche si le produit est dans le panier
            if ($order != null) {
                $orderProducts = $order[0]->getOrderProducts();
                foreach ($orderProducts as $orderProduct) {
                    // Si le produit est dans le panier, on ajoute la quantité et le libelle dans un array
                    if ($orderProduct->getProductId()->getId() == $productId) {
                        array_push($result, $orderProduct->getQuantity());
                        array_push($result, "Mettre à jour");
                    }
                }
            }      
        }
        array_push($result, 0);
        array_push($result, "Ajouter au panier");
        return $result;
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
    * Route pour supprimer un utilisateur 
    **/ 
    #[Route('/delete', name: 'app_register_delete')]
    public function delete(Security $security, EntityManagerInterface $entityManager): Response
    {
        // On récupère l'utilisateur et on le supprime (ajout de la date de supprssion)
        $user = $security->getUser();
        $user->setDeleteDate(new \DateTimeImmutable());

        // On récupère les commandes de l'utilisateur et on les supprime
        $orders = $user->getOrders();
        foreach($orders as $order) {
            if ($order->getDeleteDate() == null) {
                $order->setDeleteDate(new \DateTimeImmutable());
            }
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_logout');
    }
}

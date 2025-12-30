<?php

namespace App\Controller;
use App\Entity\User;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class PageModifierProfilcontroller extends AbstractController
{
    #[Route(path: '/pagemodifier', name: 'pagemodifier')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
      $user = $this->getUser();

if (!$user instanceof User) {
    return $this->redirectToRoute('app_login');
}

        $form = $this->createForm(UserType::class, $user, [
            'current_user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Nouveau mot de passe (champ non mappé)
            $plainPassword = $form->get('plainPassword')->getData();

            // ✅ Si l'utilisateur a saisi un mdp, on le hash et on le met dans password
            if (!empty($plainPassword)) {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $plainPassword)
                );
            }

            // ✅ L'utilisateur est déjà "managed" par Doctrine
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('pagemodifier');
        }

        return $this->render('modifier/pagemodifier.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}

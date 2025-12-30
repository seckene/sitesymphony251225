<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function index(): Response
    {
            $user = $this->getUser();

  

    if (!$user) {
        throw new AccessDeniedException('Vous devez être connecté pour accéder à cette page.');
    }

        return $this->render('profil/index.html.twig', [
               'user' => $user,
        ]);
    }
}

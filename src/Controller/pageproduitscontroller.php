<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class pageproduitscontroller extends AbstractController
{
    #[Route('/pageproduits', name: 'pageproduits')]
    public function index(Request $request, ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();

        return $this->render('pageproduits/pageproduits.html.twig', [
            'produits' => $produits,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/produit')]
final class ProduitdetailleController extends AbstractController
{
    #[Route('/detail/{id}', name: 'app_produit_show_detaille', methods: ['GET'])]
    public function showdetaille(Produit $produit): Response
    {
        return $this->render('produit/show_detaille.html.twig', [
            'produit' => $produit,
        ]);
    }
}

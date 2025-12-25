<?php
 
namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
 
class HomeController extends AbstractController
{
      #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->findAll(); // ⚡ variable en minuscule
        

        return $this->render('home/index.html.twig', [
            'produit' => $produit, // ⚡ clé en minuscule pour Twig
            'user' => $this->getUser()
        ]);
    }

 
   
}
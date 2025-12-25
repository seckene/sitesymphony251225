<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'app_panier_index', methods: ['GET'])]
    public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        // panier = [ produitId => quantite ]
        $panier = $session->get('panier', []);

        $items = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $produitRepository->find($id);
            if (!$produit) {
                continue;
            }

            $sousTotal = $produit->getPrix() * $quantite;
            $total += $sousTotal;

            $items[] = [
                'produit' => $produit,
                'quantite' => $quantite,
                'sousTotal' => $sousTotal,
            ];
        }

        return $this->render('panier/index.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'app_panier_ajouter', methods: ['POST', 'GET'])]
    public function ajouter(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId();

        if (!isset($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/retirer/{id}', name: 'app_panier_retirer', methods: ['POST', 'GET'])]
    public function retirer(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId();

        if (isset($panier[$id])) {
            $panier[$id]--;

            if ($panier[$id] <= 0) {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/supprimer/{id}', name: 'app_panier_supprimer', methods: ['POST', 'GET'])]
    public function supprimer(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId();

        unset($panier[$id]);

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/vider', name: 'app_panier_vider', methods: ['POST', 'GET'])]
    public function vider(SessionInterface $session): Response
    {
        $session->remove('panier');
        return $this->redirectToRoute('app_panier_index');
    }
}

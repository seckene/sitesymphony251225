<?php

namespace App\Controller;

use App\Entity\ProduitVariant;
use App\Repository\ProduitVariantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'app_panier_index', methods: ['GET'])]
    public function index(SessionInterface $session, ProduitVariantRepository $variantRepository): Response
    {
        // panier = [ variantId => quantite ]
        $panier = $session->get('panier', []);

        $items = [];
        $total = 0;

        foreach ($panier as $variantId => $quantite) {
            /** @var ProduitVariant|null $variant */
            $variant = $variantRepository->find($variantId);
            if (!$variant) {
                continue;
            }

            $produit = $variant->getProduit();
            if (!$produit) {
                continue;
            }

            $prix = (float) $produit->getPrix();
            $sousTotal = $prix * (int) $quantite;
            $total += $sousTotal;

            $items[] = [
                'produit' => $produit,
                'variant' => $variant,
                'quantite' => (int) $quantite,
                'sousTotal' => $sousTotal,
            ];
        }

        return $this->render('panier/index.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    /**
     * ✅ Ajouter via POST depuis la fiche produit (on envoie variant_id)
     */
    #[Route('/ajouter', name: 'app_panier_ajouter', methods: ['POST'])]
    public function ajouter(Request $request, SessionInterface $session, ProduitVariantRepository $variantRepository): Response
    {
        $variantId = (int) $request->request->get('variant_id');

        if (!$variantId) {
            $this->addFlash('danger', 'Veuillez choisir une taille.');
            return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_panier_index'));
        }

        $variant = $variantRepository->find($variantId);

        if (!$variant) {
            $this->addFlash('danger', 'Taille introuvable.');
            return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_panier_index'));
        }

        if ($variant->getStock() <= 0) {
            $this->addFlash('danger', 'Cette taille est en rupture.');
            return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('app_panier_index'));
        }

        $panier = $session->get('panier', []);
        $panier[$variantId] = ($panier[$variantId] ?? 0) + 1;

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    /**
     * ✅ +1 depuis la page panier
     */
    #[Route('/plus/{id}', name: 'app_panier_plus', methods: ['GET', 'POST'])]
    public function plus(ProduitVariant $variant, SessionInterface $session): Response
    {
        if ($variant->getStock() <= 0) {
            $this->addFlash('danger', 'Stock insuffisant.');
            return $this->redirectToRoute('app_panier_index');
        }

        $panier = $session->get('panier', []);
        $id = $variant->getId();
        $panier[$id] = ($panier[$id] ?? 0) + 1;

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    /**
     * ✅ -1 depuis la page panier
     */
    #[Route('/moins/{id}', name: 'app_panier_moins', methods: ['GET', 'POST'])]
    public function moins(ProduitVariant $variant, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $variant->getId();

        if (isset($panier[$id])) {
            $panier[$id]--;

            if ($panier[$id] <= 0) {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    /**
     * ✅ Supprimer la ligne depuis la page panier
     */
    #[Route('/supprimer/{id}', name: 'app_panier_supprimer', methods: ['GET', 'POST'])]
    public function supprimer(ProduitVariant $variant, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        unset($panier[$variant->getId()]);
        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/vider', name: 'app_panier_vider', methods: ['GET', 'POST'])]
    public function vider(SessionInterface $session): Response
    {
        $session->remove('panier');
        return $this->redirectToRoute('app_panier_index');
    }
}

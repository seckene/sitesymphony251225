<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\ProduitImage;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/produit')]
final class ProduitController extends AbstractController
{
    #[Route('', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ================= IMAGE PRINCIPALE =================
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('produits_images_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image principale.");
                    return $this->redirectToRoute('app_produit_new');
                }

                $produit->setPhoto($newFilename);
            } else {
                $produit->setPhoto(null);
            }

            // ================= GALERIE (MULTI) =================
            /** @var UploadedFile[] $galleryFiles */
            $galleryFiles = $form->get('gallery')->getData();

            foreach ($galleryFiles as $file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move($this->getParameter('produits_images_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', "Erreur lors de l'upload d'une image de la galerie.");
                    return $this->redirectToRoute('app_produit_new');
                }

                $img = new ProduitImage();
                $img->setFilename($newFilename);
                $produit->addProduitImage($img); // cascade persist => OK
            }
// ...

/** @var UploadedFile[] $galleryFiles */
$galleryFiles = $form->get('gallery')->getData() ?? [];

foreach ($galleryFiles as $file) {
    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $slugger->slug($originalFilename);
    $galleryFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

    try {
        $file->move(
            $this->getParameter('produits_images_directory'),
            $galleryFilename
        );
    } catch (FileException $e) {
        $this->addFlash('danger', "Erreur lors de l'upload d'une image de la galerie.");
        return $this->redirectToRoute('app_produit_new');
    }

    $img = new ProduitImage();
    $img->setFilename($galleryFilename);
    $produit->addProduitImage($img);
}

            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ================= IMAGE PRINCIPALE (OPTIONNEL) =================
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('produits_images_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', "Erreur lors de l'upload de l'image principale.");
                    return $this->redirectToRoute('app_produit_edit', ['id' => $produit->getId()]);
                }

                $produit->setPhoto($newFilename);
            }

            // ================= GALERIE (AJOUT D'IMAGES) =================
            /** @var UploadedFile[] $galleryFiles */
            $galleryFiles = $form->get('gallery')->getData();

            foreach ($galleryFiles as $file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move($this->getParameter('produits_images_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', "Erreur lors de l'upload d'une image de la galerie.");
                    return $this->redirectToRoute('app_produit_edit', ['id' => $produit->getId()]);
                }

                $img = new ProduitImage();
                $img->setFilename($newFilename);
                $produit->addProduitImage($img);
            }

            $em->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(), // ✅ important
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $em->remove($produit);
            $em->flush();
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}

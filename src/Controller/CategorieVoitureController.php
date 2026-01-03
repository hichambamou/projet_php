<?php

namespace App\Controller;

use App\Entity\CategorieVoiture;
use App\Form\CategorieVoitureType;
use App\Repository\CategorieVoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/categories')]
class CategorieVoitureController extends AbstractController
{
    #[Route('', name: 'admin_categorie_index', methods: ['GET'])]
    public function index(CategorieVoitureRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findWithVoitureCount();
        
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new CategorieVoiture();
        $form = $this->createForm(CategorieVoitureType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès.');

            return $this->redirectToRoute('admin_categorie_index');
        }

        return $this->render('admin/categories/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_show', methods: ['GET'])]
    public function show(CategorieVoiture $categorie): Response
    {
        return $this->render('admin/categories/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieVoiture $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieVoitureType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie mise à jour avec succès.');

            return $this->redirectToRoute('admin_categorie_index');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieVoiture $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            if ($categorie->getVoitures()->count() > 0) {
                $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle contient des voitures.');
                return $this->redirectToRoute('admin_categorie_index');
            }

            $entityManager->remove($categorie);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_categorie_index');
    }
}

<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Entity\CategorieVoiture;
use App\Repository\CategorieVoitureRepository;
use App\Form\CategorieVoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        $voitures = $em->getRepository(Voiture::class)->findBy(
            ['statut' => 'disponible'],
            ['id' => 'DESC'],
            6
        );

        return $this->render('home/index.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    #[Route('/categories', name: 'app_categorie_index', methods: ['GET'])]
    public function categories(CategorieVoitureRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
        
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Admin category routes
    #[Route('/admin/categories', name: 'admin_categorie_index', methods: ['GET'])]
    public function adminCategories(CategorieVoitureRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findWithVoitureCount();
        
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/categories/new', name: 'admin_categorie_new', methods: ['GET', 'POST'])]
    public function adminNewCategory(Request $request, EntityManagerInterface $entityManager): Response
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

    #[Route('/admin/categories/{id}', name: 'admin_categorie_show', methods: ['GET'])]
    public function adminShowCategory(CategorieVoiture $categorie): Response
    {
        return $this->render('admin/categories/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/admin/categories/{id}/edit', name: 'admin_categorie_edit', methods: ['GET', 'POST'])]
    public function adminEditCategory(Request $request, CategorieVoiture $categorie, EntityManagerInterface $entityManager): Response
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

    #[Route('/admin/categories/{id}', name: 'admin_categorie_delete', methods: ['POST'])]
    public function adminDeleteCategory(Request $request, CategorieVoiture $categorie, EntityManagerInterface $entityManager): Response
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

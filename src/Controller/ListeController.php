<?php

namespace App\Controller;

use App\Entity\Liste;
use App\Form\ListeType;
use App\Repository\ListeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/liste')]
final class ListeController extends AbstractController
{
    #[Route('/', name: 'app_liste', methods: ['GET'])]
    public function index(ListeRepository $listeRepository): Response
    {
        return $this->render('liste/index.html.twig', [
            'listes' => $listeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_liste_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $liste = new Liste();
        $form = $this->createForm(ListeType::class, $liste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($liste);
            $em->flush();

            $this->addFlash('success', 'Liste créée avec succès !');
            return $this->redirectToRoute('app_liste');
        }

        return $this->render('liste/new.html.twig', [
            'liste' => $liste,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_liste_show', methods: ['GET'])]
    public function show(Liste $liste): Response
    {
        return $this->render('liste/show.html.twig', [
            'liste' => $liste,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_liste_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Liste $liste, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ListeType::class, $liste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Liste modifiée avec succès !');
            return $this->redirectToRoute('app_liste');
        }

        return $this->render('liste/edit.html.twig', [
            'liste' => $liste,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_liste_delete', methods: ['POST'])]
    public function delete(Request $request, Liste $liste, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$liste->getId(), $request->request->get('_token'))) {
            $em->remove($liste);
            $em->flush();
            $this->addFlash('success', 'Liste supprimée avec succès !');
        }

        return $this->redirectToRoute('app_liste');
    }
}

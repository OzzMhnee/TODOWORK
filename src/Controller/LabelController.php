<?php

namespace App\Controller;

use App\Entity\Label;
use App\Form\LabelType;
use App\Repository\LabelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/label')]
final class LabelController extends AbstractController
{
    #[Route('/', name: 'app_label', methods: ['GET'])]
    public function index(LabelRepository $labelRepository): Response
    {
        return $this->render('label/index.html.twig', [
            'labels' => $labelRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_label_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $label = new Label();
        $form = $this->createForm(LabelType::class, $label);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($label);
            $em->flush();

            $this->addFlash('success', 'Label créé avec succès !');
            return $this->redirectToRoute('app_label');
        }

        return $this->render('label/new.html.twig', [
            'label' => $label,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_label_show', methods: ['GET'])]
    public function show(Label $label): Response
    {
        return $this->render('label/show.html.twig', [
            'label' => $label,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_label_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Label $label, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LabelType::class, $label);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Label modifié avec succès !');
            return $this->redirectToRoute('app_label');
        }

        return $this->render('label/edit.html.twig', [
            'label' => $label,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_label_delete', methods: ['POST'])]
    public function delete(Request $request, Label $label, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$label->getId(), $request->request->get('_token'))) {
            $em->remove($label);
            $em->flush();
            $this->addFlash('success', 'Label supprimé avec succès !');
        }

        return $this->redirectToRoute('app_label');
    }
}

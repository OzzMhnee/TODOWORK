<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/card')]
final class CardController extends AbstractController
{
    #[Route('/', name: 'app_card', methods: ['GET'])]
    public function index(CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_card_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user && $user instanceof \App\Entity\User) {
                $card->setCreatedBy($user);
            }
            $em->persist($card);
            $em->flush();

            $this->addFlash('success', 'Carte créée avec succès !');
            return $this->redirectToRoute('app_card');
        }

        return $this->render('card/new.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_card_show', methods: ['GET', 'POST'])]
    public function show(Card $card, Request $request, EntityManagerInterface $em): Response
    {
        // Gestion du formulaire de commentaire
        $comment = new \App\Entity\Comment();
        $form = $this->createForm(\App\Form\CommentType::class, $comment);
        $form->handleRequest($request);

        // Gestion du formulaire de checklist
        $checklist = new \App\Entity\Checklist();
        $checklist->setCard($card); // Pré-affecte la card à la checklist
        $checklistForm = $this->createForm(\App\Form\ChecklistType::class, $checklist);
        $checklistForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user || !$user instanceof \App\Entity\User) {
                $this->addFlash('error', 'Vous devez être connecté pour commenter.');
                return $this->redirectToRoute('app_login');
            }
            $comment->setAuthor($user);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setCard($card);
            try {
                $em->persist($comment);
                $em->flush();
                $this->addFlash('success', 'Commentaire ajouté !');
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Erreur lors de l\'ajout du commentaire : ' . $e->getMessage());
            }
            return $this->redirectToRoute('app_card_show', ['id' => $card->getId()]);
        }

        if ($checklistForm->isSubmitted() && $checklistForm->isValid()) {
            $checklist->setCard($card);

            // Attribue la prochaine position disponible
            $maxPosition = 0;
            foreach ($card->getChecklists() as $existingChecklist) {
                if ($existingChecklist->getPosition() !== null && $existingChecklist->getPosition() > $maxPosition) {
                    $maxPosition = $existingChecklist->getPosition();
                }
            }
            $checklist->setPosition($maxPosition + 1);

            $em->persist($checklist);
            $em->flush();
            $this->addFlash('success', 'Checklist ajoutée !');
            return $this->redirectToRoute('app_card_show', ['id' => $card->getId()]);
        }

        return $this->render('card/show.html.twig', [
            'card' => $card,
            'commentForm' => $form->createView(),
            'checklistForm' => $checklistForm->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_card_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Card $card, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Carte modifiée avec succès !');
            return $this->redirectToRoute('app_card');
        }

        return $this->render('card/edit.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/archive', name: 'app_card_archive', methods: ['POST'])]
    public function archive(Request $request, Card $card, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $project = $card->getListe()?->getBoard()?->getProject();

        $isOwner = false;
        if ($project && $project->getCreatedBy() && $user instanceof \App\Entity\User) {
            $isOwner = $project->getCreatedBy()->getId() === $user->getId();
        }
        $isEditor = false;
        if ($project && $user instanceof \App\Entity\User) {
            foreach ($project->getMemberShips() as $membership) {
                if ($membership->getPerson() && $membership->getPerson()->getId() === $user->getId() && $membership->getRole() === 'editor') {
                    $isEditor = true;
                    break;
                }
            }
        }

        if (!$isOwner && !$isEditor) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'archiver cette carte.');
            return $this->redirectToRoute('app_card');
        }

        if ($this->isCsrfTokenValid('archive'.$card->getId(), $request->request->get('_token'))) {
            $card->setArchivedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Carte archivée avec succès !');
        }

        return $this->redirectToRoute('app_card');
    }

    #[Route('/{id}', name: 'app_card_delete', methods: ['POST'])]
    public function delete(Request $request, Card $card, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $em->remove($card);
            $em->flush();
            $this->addFlash('success', 'Carte supprimée avec succès !');
        }

        return $this->redirectToRoute('app_card');
    }

    #[Route('/{id}/schedule', name: 'app_card_schedule', methods: ['POST'])]
    public function scheduleCard(Request $request, Card $card, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (array_key_exists('scheduled_at', $data)) {
            $card->setScheduledAt(
                $data['scheduled_at']
                    ? new \DateTimeImmutable($data['scheduled_at'], new \DateTimeZone('UTC'))
                    : null
            );
        }
        if (array_key_exists('scheduled_end_at', $data)) {
            $card->setScheduledEndAt(
                $data['scheduled_end_at']
                    ? new \DateTimeImmutable($data['scheduled_end_at'], new \DateTimeZone('UTC'))
                    : null
            );
        }
        if (isset($data['eisenhower_quadrant'])) {
            $card->setEisenhowerQuadrant($data['eisenhower_quadrant']);
        }
        $em->flush();
        return $this->json(['success' => true]);
    }
}

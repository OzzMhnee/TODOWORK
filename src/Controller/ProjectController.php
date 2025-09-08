<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_project', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }

        // Projets dont je suis owner (created_by)
        $owned = $projectRepository->findBy(['created_by' => $user]);

        // Projets où je suis membre (via MemberShip)
        $memberProjects = [];
        foreach ($user->getMemberShips() as $membership) {
            $project = $membership->getProject();
            if ($project) {
                $memberProjects[$project->getId()] = $project;
            }
        }

        // Fusionne et supprime les doublons
        $projects = $owned;
        foreach ($memberProjects as $p) {
            if (!in_array($p, $projects, true)) {
                $projects[] = $p;
            }
        }

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof \App\Entity\User) {
            $this->addFlash('error', 'Vous devez être connecté pour créer un projet.');
            return $this->redirectToRoute('app_login');
        }

        // Récupère les workspaces accessibles
        $workspaces = [];
        foreach ($user->getWorkspaces() as $ws) {
            $workspaces[$ws->getId()] = $ws;
        }
        foreach ($user->getMemberShips() as $membership) {
            if ($membership->getRole() === 'editor') {
                $project = $membership->getProject();
                if ($project && $project->getWorkspace()) {
                    $ws = $project->getWorkspace();
                    $workspaces[$ws->getId()] = $ws;
                }
            }
        }

        if (count($workspaces) === 0) {
            $this->addFlash('error', 'Vous n\'avez accès à aucun workspace pour créer un projet. Créez d\'abord un workspace ou demandez à être ajouté comme éditeur.');
            return $this->redirectToRoute('app_workspace_index');
        }

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setCreatedBy($user);

            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'Projet créé avec succès !');
            return $this->redirectToRoute('app_project');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/schedule', name: 'app_schedule')]
    public function schedule(\App\Repository\CardRepository $cardRepository, \App\Repository\ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        // Récupère toutes les cards accessibles (owner ou membership)
        $cards = [];
        if ($user && $user instanceof \App\Entity\User) {
            // Cards dont je suis créateur
            foreach ($cardRepository->findBy(['created_by' => $user]) as $c) {
                $cards[$c->getId()] = $c;
            }
            // Cards des projets où je suis membre
            foreach ($user->getMemberShips() as $membership) {
                $project = $membership->getProject();
                if ($project) {
                    foreach ($project->getBoards() as $board) {
                        foreach ($board->getListes() as $liste) {
                            foreach ($liste->getCards() as $c) {
                                $cards[$c->getId()] = $c;
                            }
                        }
                    }
                }
            }
        }
        $cards = array_values($cards);

        // Projets accessibles (owner ou membership)
        $projects = [];
        if ($user && $user instanceof \App\Entity\User) {
            // Owner
            foreach ($projectRepository->findBy(['created_by' => $user]) as $p) {
                if (!$p->getArchivedAt()) {
                    $projects[$p->getId()] = $p;
                }
            }
            // Membership
            foreach ($user->getMemberShips() as $membership) {
                $p = $membership->getProject();
                if ($p && !$p->getArchivedAt()) {
                    $projects[$p->getId()] = $p;
                }
            }
        }

        // Cards placées sur le calendrier
        $calendarEvents = [];
        foreach ($cards as $card) {
            // Affiche uniquement les cards planifiées par l'utilisateur connecté
            if ($card->getScheduledAt() && !$card->getArchivedAt() && $card->getScheduledBy() && $card->getScheduledBy()->getId() === $user->getId()) {
                $color = $card->getListe() && $card->getListe()->getBoard() && $card->getListe()->getBoard()->getProject() && $card->getListe()->getBoard()->getProject()->getLabel()
                    ? $card->getListe()->getBoard()->getProject()->getLabel()->getColor()
                    : '#3b82f6';

                // Ajout des commentaires
                $comments = [];
                foreach ($card->getComments() as $comment) {
                    $comments[] = [
                        'author' => $comment->getAuthor() ? $comment->getAuthor()->getFirstName() . ' ' . $comment->getAuthor()->getLastName() : null,
                        'content' => $comment->getContent(),
                        'date' => $comment->getCreatedAt() ? $comment->getCreatedAt()->format('d/m/Y H:i') : null,
                    ];
                }

                $calendarEvents[] = [
                    'id' => $card->getId(),
                    'title' => $card->getTitle(),
                    // Format ISO 8601 UTC (ex: 2025-09-04T12:00:00Z) requis par FullCalendar pour éviter tout décalage horaire
                    'start' => $card->getScheduledAt() ? $card->getScheduledAt()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z') : null,
                    'end' => $card->getScheduledEndAt() ? $card->getScheduledEndAt()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z') : null,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'description' => $card->getDescription(),
                    'comments' => $comments,
                ];
            }
        }

        return $this->render('schedule/index.html.twig', [
            'cards' => $cards,
            'calendarEvents' => $calendarEvents,
            'projects' => $projects,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProjectType::class, $project, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Projet modifié avec succès !');
            return $this->redirectToRoute('app_project');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $em->remove($project);
            $em->flush();
            $this->addFlash('success', 'Projet supprimé avec succès !');
        }

        return $this->redirectToRoute('app_project');
    }

}

<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\User;
use App\Form\BoardType;
use App\Repository\BoardRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/board')]
final class BoardController extends AbstractController
{
    #[Route('/', name: 'app_board', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof User) {
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
        $boards = [];
        foreach ($projects as $p) {
            foreach ($p->getBoards() as $b) {
                $boards[$b->getId()] = $b;
            }
        }
        $boards = array_values($boards);

        return $this->render('board/index.html.twig', [
            'boards' => $boards,
        ]);
    }

    #[Route('/new', name: 'app_board_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $board = new Board();
        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($board);
            $em->flush();

            $this->addFlash('success', 'Board créé avec succès !');
            return $this->redirectToRoute('app_board');
        }

        return $this->render('board/new.html.twig', [
            'board' => $board,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_board_show', methods: ['GET'])]
    public function show(Board $board): Response
    {
        return $this->render('board/show.html.twig', [
            'board' => $board,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_board_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Board $board, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Board modifié avec succès !');
            return $this->redirectToRoute('app_board');
        }

        return $this->render('board/edit.html.twig', [
            'board' => $board,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_board_delete', methods: ['POST'])]
    public function delete(Request $request, Board $board, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$board->getId(), $request->request->get('_token'))) {
            $em->remove($board);
            $em->flush();
            $this->addFlash('success', 'Board supprimé avec succès !');
        }

        return $this->redirectToRoute('app_board');
    }
}

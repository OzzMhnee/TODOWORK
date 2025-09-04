<?php

namespace App\Controller;

use App\Entity\Workspace;
use App\Form\WorkspaceType;
use App\Repository\WorkspaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/workspace')]
final class WorkspaceController extends AbstractController
{
    #[Route('/', name: 'app_workspace_index', methods: ['GET'])]
    public function index(WorkspaceRepository $workspaceRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }

        // Workspaces dont je suis owner
        $owned = $workspaceRepository->findBy(['owner' => $user]);

        // Workspaces où je suis membre (via MemberShip sur un Project du workspace)
        $memberWorkspaces = [];
        foreach ($user->getMemberShips() as $membership) {
            $project = $membership->getProject();
            if ($project && $project->getWorkspace()) {
                $memberWorkspaces[$project->getWorkspace()->getId()] = $project->getWorkspace();
            }
        }

        // Fusionne et supprime les doublons
        $workspaces = $owned;
        foreach ($memberWorkspaces as $ws) {
            if (!in_array($ws, $workspaces, true)) {
                $workspaces[] = $ws;
            }
        }

        return $this->render('workspace/index.html.twig', [
            'workspaces' => $workspaces,
        ]);
    }

    #[Route('/new', name: 'app_workspace_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $workspace = new Workspace();
        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('error', 'Vous devez être connecté pour créer un workspace.');
                return $this->redirectToRoute('app_login');
            }
            $workspace->setOwner($user);

            $em->persist($workspace);
            $em->flush();
            $this->addFlash('success','Votre Espace de Travail a bien été créé.');
            return $this->redirectToRoute('app_workspace_index');
        }

        return $this->render('workspace/new.html.twig', [
            'workspace' => $workspace,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_workspace_show', methods: ['GET'])]
    public function show(Workspace $workspace): Response
    {
        return $this->render('workspace/show.html.twig', [
            'workspace' => $workspace,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workspace_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Workspace $workspace, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(WorkspaceType::class, $workspace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success','Votre Espace de Travail a bien été modifié.');
            return $this->redirectToRoute('app_workspace_index');
        }

        return $this->render('workspace/edit.html.twig', [
            'workspace' => $workspace,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_workspace_delete', methods: ['POST'])]
    public function delete(Request $request, Workspace $workspace, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workspace->getId(), $request->request->get('_token'))) {
            $em->remove($workspace);
            $em->flush();
            $this->addFlash('success','Votre Espace de Travail a bien été supprimé.');
        }

        return $this->redirectToRoute('app_workspace_index');
    }
    #[Route('/{id}/invite', name: 'app_workspace_invite', methods: ['GET', 'POST'])]
    public function inviteMember(
        Request $request,
        Workspace $workspace,
        \App\Repository\UserRepository $userRepository,
        \App\Repository\ProjectRepository $projectRepository,
        EntityManagerInterface $em
    ): Response
    {
        $user = $this->getUser();
        if (!$user || $workspace->getOwner() !== $user) {
            throw $this->createAccessDeniedException('Seul le propriétaire du workspace peut inviter des membres.');
        }

        $form = $this->createForm(\App\Form\InviteWorkspaceMemberType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $invitedUser = $userRepository->findOneBy(['email' => $data['email']]);
            if (!$invitedUser) {
                $this->addFlash('error', 'La personne n\'est pas encore inscrite sur le site. Elle doit d\'abord créer un compte avant de pouvoir être ajoutée au workspace.');
                return $this->redirectToRoute('app_workspace_invite', ['id' => $workspace->getId()]);
            } else {
                // Ajoute le membre à tous les projets du workspace (ou adapte selon ton besoin)
                $alreadyMember = false;
                foreach ($workspace->getProjects() as $project) {
                    foreach ($project->getMemberShips() as $membership) {
                        if ($membership->getPerson() === $invitedUser) {
                            $alreadyMember = true;
                            break 2;
                        }
                    }
                }
                if ($alreadyMember) {
                    $this->addFlash('error', 'Cet utilisateur est déjà membre d\'au moins un projet du workspace.');
                } else {
                    foreach ($workspace->getProjects() as $project) {
                        $membership = new \App\Entity\MemberShip();
                        $membership->setProject($project);
                        $membership->setPerson($invitedUser);
                        $membership->setRole($data['role']);
                        $em->persist($membership);
                    }
                    $em->flush();
                    $this->addFlash('success', 'Membre ajouté avec succès à tous les projets du workspace !');
                    return $this->redirectToRoute('app_workspace_show', ['id' => $workspace->getId()]);
                }
            }
        }

        return $this->render('workspace/invite_member.html.twig', [
            'form' => $form->createView(),
            'workspace' => $workspace,
        ]);
    }
}

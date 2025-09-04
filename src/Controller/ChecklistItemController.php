<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Checklist;
use App\Entity\ChecklistItem;
use App\Repository\ChecklistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ChecklistItemController extends AbstractController
{
    #[Route('/checklist/item', name: 'app_checklist_item')]
    public function index(): Response
    {
        return $this->render('checklist_item/index.html.twig', [
            'controller_name' => 'ChecklistItemController',
        ]);
    }

    #[Route('/checklist/{checklistId}/item/add', name: 'app_checklist_item_add', methods: ['POST'])]
    public function add(
        int $checklistId,
        Request $request,
        ChecklistRepository $checklistRepo,
        EntityManagerInterface $em
    ): RedirectResponse {
        $content = trim($request->request->get('content', ''));
        if (!$content) {
            $this->addFlash('error', 'Le contenu de l\'item ne peut pas être vide.');
            return $this->redirect($request->headers->get('referer') ?: '/');
        }
        $checklist = $checklistRepo->find($checklistId);
        if (!$checklist) {
            $this->addFlash('error', 'Checklist introuvable.');
            return $this->redirect($request->headers->get('referer') ?: '/');
        }

        // Calcul de la prochaine position
        $maxPosition = 0;
        foreach ($checklist->getChecklistItems() as $item) {
            if ($item->getPosition() !== null && $item->getPosition() > $maxPosition) {
                $maxPosition = $item->getPosition();
            }
        }

        $item = new ChecklistItem();
        $item->setChecklist($checklist);
        $item->setContent($content);
        $item->setIsDone(false);
        $item->setPosition($maxPosition + 1);

        $em->persist($item);
        $em->flush();

        $this->addFlash('success', 'Item ajouté à la checklist.');
        return $this->redirect($request->headers->get('referer') ?: '/');
    }

    #[Route('/checklist/item/{id}/toggle', name: 'app_checklist_item_toggle', methods: ['POST'])]
    public function toggle(
        int $id,
        Request $request,
        \App\Repository\ChecklistItemRepository $itemRepo,
        EntityManagerInterface $em
    ): \Symfony\Component\HttpFoundation\JsonResponse {
        $item = $itemRepo->find($id);
        if (!$item) {
            return $this->json(['success' => false, 'error' => 'Item introuvable'], 404);
        }
        $data = json_decode($request->getContent(), true);

        $isDone = filter_var($data['is_done'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $item->setIsDone($isDone === true);

        $em->flush();
        return $this->json(['success' => true]);
    }
}

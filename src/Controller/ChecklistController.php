<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChecklistRepository;
use App\Repository\CardRepository;
use App\Repository\ChecklistItemRepository;

final class ChecklistController extends AbstractController
{
    #[Route('/checklist', name: 'app_checklist')]
    public function index(): Response
    {
        return $this->render('checklist/index.html.twig', [
            'controller_name' => 'ChecklistController',
        ]);
    }

    #[Route('/checklist/reorder', name: 'app_checklist_reorder', methods: ['POST'])]
    public function reorder(Request $request, EntityManagerInterface $em, ChecklistRepository $checklistRepo, CardRepository $cardRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$this->isCsrfTokenValid('reorder_checklists', $request->headers->get('X-CSRF-TOKEN'))) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], 400);
        }
        $card = $cardRepo->find($data['card_id'] ?? 0);
        $ids = $data['checklist_ids'] ?? [];
        if (!$card || !is_array($ids)) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid data'], 400);
        }
        foreach ($ids as $pos => $id) {
            $checklist = $checklistRepo->find($id);
            if ($checklist && $checklist->getCard() === $card) {
                $checklist->setPosition($pos + 1);
            }
        }
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/checklist/item/reorder', name: 'app_checklist_item_reorder', methods: ['POST'])]
    public function reorderItems(
        Request $request,
        EntityManagerInterface $em,
        ChecklistRepository $checklistRepo,
        ChecklistItemRepository $itemRepo
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$this->isCsrfTokenValid('reorder_checklist_items', $request->headers->get('X-CSRF-TOKEN'))) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid CSRF token'], 400);
        }
        $checklist = $checklistRepo->find($data['checklist_id'] ?? 0);
        $ids = $data['item_ids'] ?? [];
        if (!$checklist || !is_array($ids)) {
            return new JsonResponse(['success' => false, 'error' => 'Invalid data'], 400);
        }
        foreach ($ids as $pos => $id) {
            $item = $itemRepo->find($id);
            if ($item && $item->getChecklist() === $checklist) {
                $item->setPosition($pos + 1);
            }
        }
        $em->flush();
        return new JsonResponse(['success' => true]);
    }
}

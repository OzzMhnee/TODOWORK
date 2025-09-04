<?php

namespace App\Entity;

use App\Repository\ChecklistItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChecklistItemRepository::class)]
class ChecklistItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'checklistItems')]
    private ?Checklist $checklist = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $is_done = false;

    #[ORM\Column]
    private ?int $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChecklist(): ?Checklist
    {
        return $this->checklist;
    }

    public function setChecklist(?Checklist $checklist): static
    {
        $this->checklist = $checklist;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isDone(): ?bool
    {
        return $this->is_done;
    }

    public function setIsDone(bool $is_done): static
    {
        $this->is_done = (bool)$is_done;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}

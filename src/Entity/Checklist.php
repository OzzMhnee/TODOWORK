<?php

namespace App\Entity;

use App\Repository\ChecklistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChecklistRepository::class)]
class Checklist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'checklists')]
    private ?Card $card = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $position = null;

    /**
     * @var Collection<int, ChecklistItem>
     */
    #[ORM\OneToMany(targetEntity: ChecklistItem::class, mappedBy: 'checklist')]
    private Collection $checklistItems;

    public function __construct()
    {
        $this->checklistItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): static
    {
        $this->card = $card;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    /**
     * @return Collection<int, ChecklistItem>
     */
    public function getChecklistItems(): Collection
    {
        return $this->checklistItems;
    }

    public function addChecklistItem(ChecklistItem $checklistItem): static
    {
        if (!$this->checklistItems->contains($checklistItem)) {
            $this->checklistItems->add($checklistItem);
            $checklistItem->setChecklist($this);
        }

        return $this;
    }

    public function removeChecklistItem(ChecklistItem $checklistItem): static
    {
        if ($this->checklistItems->removeElement($checklistItem)) {
            // set the owning side to null (unless already changed)
            if ($checklistItem->getChecklist() === $this) {
                $checklistItem->setChecklist(null);
            }
        }

        return $this;
    }
}

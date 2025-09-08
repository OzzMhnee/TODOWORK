<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    private ?Liste $liste = null;

    #[ORM\Column]
    private ?int $position = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $due_at = null;

    #[ORM\ManyToOne(inversedBy: 'cards')]
    private ?User $created_by = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $archived_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $scheduled_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $scheduled_end_at = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $scheduled_by = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $eisenhower_quadrant = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'card', cascade: ['persist', 'remove'])]
    private Collection $comments;

    /**
     * @var Collection<int, Attachment>
     */
    #[ORM\OneToMany(targetEntity: Attachment::class, mappedBy: 'card')]
    private Collection $attachments;

    /**
     * @var Collection<int, Checklist>
     */
    #[ORM\OneToMany(targetEntity: Checklist::class, mappedBy: 'card')]
    private Collection $checklists;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->checklists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getListe(): ?Liste
    {
        return $this->liste;
    }

    public function setListe(?Liste $liste): static
    {
        $this->liste = $liste;

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

    public function getDueAt(): ?\DateTimeImmutable
    {
        return $this->due_at;
    }

    public function setDueAt(?\DateTimeImmutable $due_at): static
    {
        $this->due_at = $due_at;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getArchivedAt(): ?\DateTimeImmutable
    {
        return $this->archived_at;
    }

    public function setArchivedAt(\DateTimeImmutable $archived_at): static
    {
        $this->archived_at = $archived_at;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCard($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCard() === $this) {
                $comment->setCard(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): static
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setCard($this);
        }

        return $this;
    }

    public function removeAttachment(Attachment $attachment): static
    {
        if ($this->attachments->removeElement($attachment)) {
            // set the owning side to null (unless already changed)
            if ($attachment->getCard() === $this) {
                $attachment->setCard(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Checklist>
     */
    public function getChecklists(): Collection
    {
        return $this->checklists;
    }

    public function addChecklist(Checklist $checklist): static
    {
        if (!$this->checklists->contains($checklist)) {
            $this->checklists->add($checklist);
            $checklist->setCard($this);
        }

        return $this;
    }

    public function removeChecklist(Checklist $checklist): static
    {
        if ($this->checklists->removeElement($checklist)) {
            // set the owning side to null (unless already changed)
            if ($checklist->getCard() === $this) {
                $checklist->setCard(null);
            }
        }

        return $this;
    }


    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduled_at;
    }

    public function setScheduledAt(?\DateTimeImmutable $scheduled_at): static
    {
        $this->scheduled_at = $scheduled_at;
        return $this;
    }

    public function getEisenhowerQuadrant(): ?string
    {
        return $this->eisenhower_quadrant;
    }

    public function setEisenhowerQuadrant(?string $eisenhower_quadrant): static
    {
        $this->eisenhower_quadrant = $eisenhower_quadrant;
        return $this;
    }

    public function getScheduledEndAt(): ?\DateTimeImmutable
    {
        return $this->scheduled_end_at;
    }

    public function setScheduledEndAt(?\DateTimeImmutable $scheduled_end_at): static
    {
        $this->scheduled_end_at = $scheduled_end_at;
        return $this;
    }

    public function getScheduledBy(): ?User
    {
        return $this->scheduled_by;
    }

    public function setScheduledBy(?User $user): static
    {
        $this->scheduled_by = $user;
        return $this;
    }
}

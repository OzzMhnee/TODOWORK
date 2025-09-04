<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $archived_at = null;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Workspace $workspace = null;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?User $created_by = null;

    /**
     * @var Collection<int, Board>
     */
    #[ORM\OneToMany(targetEntity: Board::class, mappedBy: 'project')]
    private Collection $boards;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    private ?Label $label = null;

    /**
     * @var Collection<int, MemberShip>
     */
    #[ORM\OneToMany(targetEntity: MemberShip::class, mappedBy: 'project')]
    private Collection $memberShips;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
        $this->memberShips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): static
    {
        $this->workspace = $workspace;

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

    /**
     * @return Collection<int, Board>
     */
    public function getBoards(): Collection
    {
        return $this->boards;
    }

    public function addBoard(Board $board): static
    {
        if (!$this->boards->contains($board)) {
            $this->boards->add($board);
            $board->setProject($this);
        }

        return $this;
    }

    public function removeBoard(Board $board): static
    {
        if ($this->boards->removeElement($board)) {
            // set the owning side to null (unless already changed)
            if ($board->getProject() === $this) {
                $board->setProject(null);
            }
        }

        return $this;
    }

    public function getLabel(): ?Label
    {
        return $this->label;
    }

    public function setLabel(?Label $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, MemberShip>
     */
    public function getMemberShips(): Collection
    {
        return $this->memberShips;
    }

    public function getArchivedAt(): ?\DateTimeImmutable
    {
        return $this->archived_at;
    }

    public function setArchivedAt(?\DateTimeImmutable $archived_at): static
    {
        $this->archived_at = $archived_at;
        return $this;
    }

    public function addMemberShip(MemberShip $memberShip): static
    {
        if (!$this->memberShips->contains($memberShip)) {
            $this->memberShips->add($memberShip);
            $memberShip->setProject($this);
        }

        return $this;
    }

    public function removeMemberShip(MemberShip $memberShip): static
    {
        if ($this->memberShips->removeElement($memberShip)) {
            // set the owning side to null (unless already changed)
            if ($memberShip->getProject() === $this) {
                $memberShip->setProject(null);
            }
        }

        return $this;
    }
}

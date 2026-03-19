<?php

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateHeure = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column]
    private ?int $nbPlacesMax = null;

    #[ORM\ManyToOne(inversedBy: 'seances', targetEntity: Cours::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Cours $cours = null;

    #[ORM\OneToMany(mappedBy: 'seance', targetEntity: Inscription::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeure(): ?\DateTime
    {
        return $this->dateHeure;
    }

    public function setDateHeure(\DateTime $dateHeure): static
    {
        $this->dateHeure = $dateHeure;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getNbPlacesMax(): ?int
    {
        return $this->nbPlacesMax;
    }

    public function setNbPlacesMax(int $nbPlacesMax): static
    {
        $this->nbPlacesMax = $nbPlacesMax;
        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static
    {
        $this->cours = $cours;
        return $this;
    }

    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function getNbInscriptions(): int
    {
        return $this->inscriptions->count();
    }

    public function getPlacesRestantes(): int
    {
        return max(0, $this->getNbPlacesMax() - $this->getNbInscriptions());
    }

    public function estComplete(): bool
    {
        return $this->getPlacesRestantes() <= 0;
    }
}
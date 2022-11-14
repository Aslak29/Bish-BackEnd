<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Monolog\DateTimeImmutable;
use Symfony\Component\Validator\Constraints\DateTime;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $pathImage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?bool $isTrend = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    #[ORM\ManyToMany(targetEntity: Categorie::class, mappedBy: 'produits')]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: ProduitBySize::class)]
    private Collection $produitBySize;

    #[ORM\OneToMany(mappedBy: 'produits',targetEntity: ProduitInCommande::class)]
    private Collection $ProduitInCommande;

    #[ORM\ManyToOne(inversedBy: 'Produits')]
    private ?Promotions $promotions = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Notation::class)]
    private Collection $Note;

    public function __construct()
    {
        $dt = new DateTimeImmutable(0);
        $dt->format('Y-m-d H:i:s');
        $this->categories = new ArrayCollection();
        $this->produitBySize = new ArrayCollection();
        $this->Note = new ArrayCollection();
        $this->ProduitInCommande = new ArrayCollection();
        $this->created_at = $dt;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPathImage(): ?string
    {
        return $this->pathImage;
    }

    public function setPathImage(string $pathImage): self
    {
        $this->pathImage = $pathImage;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isIsTrend(): ?bool
    {
        return $this->isTrend;
    }

    public function setIsTrend(bool $isTrend): self
    {
        $this->isTrend = $isTrend;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addProduit($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduit($this);
        }

        return $this;
    }

    public function getProduitInCommande(): Collection
    {
        return $this->ProduitInCommande;
    }

    public function getPromotions(): ?Promotions
    {
        return $this->promotions;
    }

    public function setPromotions(?Promotions $promotions): self
    {
        $this->promotions = $promotions;

        return $this;
    }

    /**
     * @return Collection<int, ProduitBySize>
     */
    public function getProduitBySize(): Collection
    {
        return $this->produitBySize;
    }

    public function addProduitBySize(ProduitBySize $produitBySize): self
    {
        if (!$this->produitBySize->contains($produitBySize)) {
            $this->produitBySize->add($produitBySize);
            $produitBySize->setProduit($this);
        }

        return $this;
    }

    public function removeProduitBySize(ProduitBySize $produitBySize): self
    {
        if ($this->produitBySize->removeElement($produitBySize)) {
            // set the owning side to null (unless already changed)
            if ($produitBySize->getProduit() === $this) {
                $produitBySize->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notation>
     */
    public function getNote(): Collection
    {
        return $this->Note;
    }

    public function addNote(Notation $note): self
    {
        if (!$this->Note->contains($note)) {
            $this->Note->add($note);
            $note->setProduit($this);
        }

        return $this;
    }

    public function removeNote(Notation $note): self
    {
        if ($this->Note->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getProduit() === $this) {
                $note->setProduit(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\TailleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TailleRepository::class)]
class Taille
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $taille = null;

    #[ORM\OneToMany(mappedBy: 'taille', targetEntity: ProduitBySize::class)]
    private Collection $produitBySize;

    #[ORM\Column(length: 255)]
    private ?string $type = null;


    public function __construct()
    {
        $this->produitBySize = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille): self
    {
        $this->taille = $taille;

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
            $produitBySize->setTaille($this);
        }

        return $this;
    }

    public function removeProduitBySize(ProduitBySize $produitBySize): self
    {
        if ($this->produitBySize->removeElement($produitBySize)) {
            // set the owning side to null (unless already changed)
            if ($produitBySize->getTaille() === $this) {
                $produitBySize->setTaille(null);
            }
        }

        return $this;
    }
    //get tailles 
    public function getType():string{
        return $this->type;
    }
    public function setType(string $newType){
        $this->type=$newType;
    } 
}

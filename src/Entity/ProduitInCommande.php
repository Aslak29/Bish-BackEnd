<?php

namespace App\Entity;

use App\Repository\ProduitInCommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitInCommandeRepository::class)]
class ProduitInCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'ProduitInCommande')]
    private Produit $produits;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'ProduitInCommande')]
    private Commande $commandes;

    #[ORM\Column(nullable: true)]
    private ?float $remise = null;

    #[ORM\Column(length: 10)]
    private ?string $taille = null;

    #[ORM\Column(length: 255)]
    private ?string $Ville = null;

    #[ORM\Column(length: 255)]
    private ?string $rue = null;

    #[ORM\Column]
    private ?int $codePostal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

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

    /**
     * @return Produit
     */
    public function getProduit(): Produit
    {
        return $this->produits;
    }

    /**
     * @param Produit $produits
     */
    public function setProduit(Produit $produits): void
    {
        $this->produits = $produits;
    }

    /**
     * @return Commande
     */
    public function getCommande(): Commande
    {
        return $this->commandes;
    }

    /**
     * @param Commande $commandes
     */
    public function setCommande(Commande $commandes): void
    {
        $this->commandes = $commandes;
    }

    public function getRemise(): ?float
    {
        return $this->remise;
    }

    public function setRemise(?float $remise): self
    {
        $this->remise = $remise;

        return $this;
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

    public function getVille(): ?string
    {
        return $this->Ville;
    }

    public function setVille(string $Ville): self
    {
        $this->Ville = $Ville;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }



}

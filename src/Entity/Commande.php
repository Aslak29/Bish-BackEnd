<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private DateTimeImmutable $dateFacture;

    #[ORM\Column(length:255)]
    private string $etatCommande;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'commandes',targetEntity: ProduitInCommande::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collection $ProduitInCommande;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rue = null;

    #[ORM\Column(length: 255)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numRue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complementAdresse = null;

    /**
     * @param DateTimeImmutable $dateFacture
     */
    public function __construct()
    {
        $dt = new \Monolog\DateTimeImmutable(0);
        $dt->format('Y-m-d H:i:s');
        $this->dateFacture = $dt;
        $this->ProduitInCommande = new ArrayCollection();
    }


    /**
     * Get the value of etatCommande
     */ 
    public function getEtatCommande()
    {
        return $this->etatCommande;
    }

    /**
     * Set the value of etatCommande
     *
     * @return  self
     */ 
    public function setEtatCommande($etatCommande)
    {
        $this->etatCommande = $etatCommande;

        return $this;
    }

    /**
     * Get the value of dateFacture
     */ 
    public function getDateFacture()
    {
        return $this->dateFacture;
    }

    /**
     * Set the value of dateFacture
     *
     * @return  self
     */ 
    public function setDateFacture($dateFacture)
    {
        $this->dateFacture = $dateFacture;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getProduitInCommande(): ?Collection
    {
        return $this->ProduitInCommande;
    }

    /**
     * @param Collection|null $ProduitInCommande
     */
    public function setProduitInCommande(?Collection $ProduitInCommande): void
    {
        $this->ProduitInCommande = $ProduitInCommande;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getNumRue(): ?string
    {
        return $this->numRue;
    }

    public function setNumRue(?string $numRue): self
    {
        $this->numRue = $numRue;

        return $this;
    }

    public function getComplementAdresse(): ?string
    {
        return $this->complementAdresse;
    }

    public function setComplementAdresse(?string $complementAdresse): self
    {
        $this->complementAdresse = $complementAdresse;

        return $this;
    }


}


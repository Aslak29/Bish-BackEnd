<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

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
    private ?string $villeLivraison = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rueLivraison = null;

    #[ORM\Column(length: 255)]
    private ?string $codePostalLivraison = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numRueLivraison = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complementAdresseLivraison = null;

    #[ORM\Column(length: 255)]
    private ?string $villeFacturation = null;

    #[ORM\Column(length: 255)]
    private ?string $rueFacturation = null;

    #[ORM\Column(length: 255)]
    private ?string $codePostalFacturation = null;

    #[ORM\Column(length: 255)]
    private ?string $numRueFacturation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complementAdresseFacturation = null;

    #[ORM\Column(nullable: true)]
    private ?float $remise = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $remiseType = null;

    /**
     * @param DateTimeImmutable $dateFacture
     * @throws Exception
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

    public function getVilleLivraison(): ?string
    {
        return $this->villeLivraison;
    }

    public function setVilleLivraison(string $villeLivraison): self
    {
        $this->villeLivraison = $villeLivraison;

        return $this;
    }

    public function getRueLivraison(): ?string
    {
        return $this->rueLivraison;
    }

    public function setRueLivraison(?string $rueLivraison): self
    {
        $this->rueLivraison = $rueLivraison;

        return $this;
    }

    public function getCodePostalLivraison(): ?string
    {
        return $this->codePostalLivraison;
    }

    public function setCodePostalLivraison(string $codePostalLivraison): self
    {
        $this->codePostalLivraison = $codePostalLivraison;

        return $this;
    }

    public function getNumRueLivraison(): ?string
    {
        return $this->numRueLivraison;
    }

    public function setNumRueLivraison(?string $numRueLivraison): self
    {
        $this->numRueLivraison = $numRueLivraison;

        return $this;
    }

    public function getComplementAdresseLivraison(): ?string
    {
        return $this->complementAdresseLivraison;
    }

    public function setComplementAdresseLivraison(?string $complementAdresseLivraison): self
    {
        $this->complementAdresseLivraison = $complementAdresseLivraison;

        return $this;
    }

    public function getVilleFacturation(): ?string
    {
        return $this->villeFacturation;
    }

    public function setVilleFacturation(string $villeFacturation): self
    {
        $this->villeFacturation = $villeFacturation;

        return $this;
    }

    public function getRueFacturation(): ?string
    {
        return $this->rueFacturation;
    }

    public function setRueFacturation(string $rueFacturation): self
    {
        $this->rueFacturation = $rueFacturation;

        return $this;
    }

    public function getCodePostalFacturation(): ?string
    {
        return $this->codePostalFacturation;
    }

    public function setCodePostalFacturation(string $codePostalFacturation): self
    {
        $this->codePostalFacturation = $codePostalFacturation;

        return $this;
    }

    public function getNumRueFacturation(): ?string
    {
        return $this->numRueFacturation;
    }

    public function setNumRueFacturation(string $numRueFacturation): self
    {
        $this->numRueFacturation = $numRueFacturation;

        return $this;
    }

    public function getComplementAdresseFacturation(): ?string
    {
        return $this->complementAdresseFacturation;
    }

    public function setComplementAdresseFacturation(?string $complementAdresseFacturation): self
    {
        $this->complementAdresseFacturation = $complementAdresseFacturation;

        return $this;
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

    public function getRemiseType(): ?string
    {
        return $this->remiseType;
    }

    public function setRemiseType(?string $remiseType): self
    {
        $this->remiseType = $remiseType;

        return $this;
    }


}


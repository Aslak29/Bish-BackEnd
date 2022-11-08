<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use DateTimeImmutable;
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

    #[ORM\OneToMany(mappedBy: 'commandes',targetEntity: ProduitInCommande::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProduitInCommande $ProduitInCommande = null;
    


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

    public function getProduitInCommande(): ?ProduitInCommande
    {
        return $this->ProduitInCommande;
    }

    public function setProduitInCommande(?ProduitInCommande $ProduitInCommande): self
    {
        $this->ProduitInCommande = $ProduitInCommande;

        return $this;
    }
}


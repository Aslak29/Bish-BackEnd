<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\ProduitInCommande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProduitInCommandeFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $produits = array();
        for ($i = 1 ; $i < 100 ; $i++) {
            $produits[] = $this->getReference('produit_'.$i);
        }
        
        $commandes = array();
        for ($i = 1 ; $i < 10 ; $i++) {
            $commandes[] = $this->getReference('commande_'.$i);
        }


        for ($i = 1; $i < 35; $i++) {

            $produitInCommande = new ProduitInCommande();
            $produitInCommande->setProduit($produits[rand(0, 98)]);
            $produitInCommande->setCommande($commandes[rand(0, 8)]);
            $produitInCommande->setQuantite(rand(1, 10));
            $produitInCommande->setNameProduct($produitInCommande->getProduit()->getName());
            $produitInCommande->setPrice($produitInCommande->getProduit()->getPrice());
            $produitInCommande->setRemise($produitInCommande->getProduit()->getPromotions() &&
                $produitInCommande->getProduit()->getPromotions()->getRemise());
            $produitInCommande->setTaille(
                $produitInCommande->getProduit()->getProduitBySize()->get(rand(0, 4))->getTaille()->getTaille());

            $manager->persist($produitInCommande);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProduitFixtures::class,
            CommandeFixture::class,
            ProduitBySizeFixtures::class,
        ];
    }

}
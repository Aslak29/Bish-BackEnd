<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Monolog\DateTimeImmutable;

class CommandeFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $etatCommande = ["En préparation", "En cours de livraison", "Livrée"];
        for ($i = 1; $i < 10; $i++) {
            $commande = (new Commande())
                ->setEtatCommande($etatCommande[rand(0, 2)])
                ->setUser($this->getReference('user_'.$i))
                ->setNumRueLivraison("10")
                ->setRueLivraison("rue du moulin")
                ->setVilleLivraison("lille")
                ->setCodePostalLivraison("59000")
                ->setNumRueFacturation("11")
                ->setRueFacturation("rue du pont")
                ->setVilleFacturation("lens")
                ->setCodePostalFacturation("62300")
            ;
            $this->addReference('commande_'.$i, $commande);
            $manager->persist($commande);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }

}
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
        for ($i = 1; $i < 10; $i++) {
            $commande = (new Commande())
                ->setEtatCommande("En Cours")
                ->setUser($this->getReference('user_'.$i))
                ->setNumRue("10")
                ->setRue("rue du moulin")
                ->setVille("lille")
                ->setCodePostal("59000")
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
<?php

namespace App\DataFixtures;

use App\Entity\Notation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NotationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 250; $i++) {
            $note = (new Notation())
                ->setNote(rand(1,5))
                ->setProduit($this->getReference('produit_'.$i))
                ->setUser($this->getReference('user_'.$i))
            ;
            $manager->persist($note);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ProduitFixtures::class
        ];
    }
}

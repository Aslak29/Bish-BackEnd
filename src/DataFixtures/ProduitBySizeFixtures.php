<?php

namespace App\DataFixtures;

use App\Entity\ProduitBySize;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProduitBySizeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $taille = array();
        for ($j = 0; $j < 4; $j++ ){
            $taille[] = $this->getReference('taille_'.$j);
        };
        for ($i = 1; $i < 99; $i++) {
            $produitBy = (new ProduitBySize())
                ->setStock(rand(1,100))
                ->setProduit($this->getReference('produit_'.$i))
                ->setTaille($taille[rand(0,3)])
            ;
            $manager->persist($produitBy);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            ProduitFixtures::class,
            TailleFixtures::class
        ];
    }
}

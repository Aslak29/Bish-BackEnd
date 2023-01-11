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
        $tailleAdulte = array();
        for ($j = 0; $j <= 4; $j++) {
            $tailleAdulte[] = $this->getReference('taille_'.$j);
        }

        $tailleEnfant = array();
        for ($x = 0; $x <= 4; $x++) {
            $tailleEnfant[] = $this->getReference('tailleEnfant_'.$x);
        }

        $tailleNourrisson = array();
        for ($x = 0; $x <= 4; $x++) {
            $tailleNourrisson[] = $this->getReference('tailleNourrisson_'.$x);
        }

        for ($i = 1; $i < 250; $i++) {
            if ($i < 100) {
                for ($j = 0; $j < 5; $j++) {
                    $produitBy = new ProduitBySize();
                    $produitBy->setProduit($this->getReference('produit_'.$i));
                    $produitBy->setTaille($tailleAdulte[$j]);
                    $produitBy->setStock(rand(0, 100));
                    $manager->persist($produitBy);
                }
            }elseif ($i < 200) {
                for ($w = 0; $w < 5; $w++) {
                    $produitBy = new ProduitBySize();
                    $produitBy->setProduit($this->getReference('produit_'.$i));
                    $produitBy->setTaille($tailleEnfant[$w]);
                    $produitBy->setStock(rand(0, 100));
                    $manager->persist($produitBy);
                }
            }elseif ($i < 250) {
                for ($y = 0; $y < 5; $y++) {
                    $produitBy = new ProduitBySize();
                    $produitBy->setProduit($this->getReference('produit_'.$i));
                    $produitBy->setTaille($tailleNourrisson[$y]);
                    $produitBy->setStock(rand(0, 100));
                    $manager->persist($produitBy);
                }
            }
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

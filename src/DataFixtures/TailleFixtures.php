<?php

namespace App\DataFixtures;

use App\Entity\Taille;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TailleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tabTaille = array('xs','s','m','l','xl');
        $tabTailleNourrisson = array('2M','4M','6M','1A','2A');
        $tabTailleEnfant = array('6A', '8A', '10A', '12A', '14A');

        for ($i = 0; $i < count($tabTaille); $i++) {
            $taille = new Taille();
            $taille ->setTaille($tabTaille[$i]);
            $taille ->setType("Adulte");
            $this->addReference('taille_'.$i, $taille);
            $manager->persist($taille);
        }
        
        for ($j = 0; $j < count($tabTailleNourrisson); $j++) {
            $taille = new Taille();
            $taille ->setTaille($tabTailleNourrisson[$j]);
            $taille ->setType("Nourrisson");
            $this->addReference('tailleNourrisson_'.$j, $taille);
            $manager->persist($taille);
        }
        for ($k = 0; $k < count($tabTailleEnfant); $k++) {
            $taille = new Taille();
            $taille ->setTaille($tabTailleEnfant[$k]);
            $taille ->setType("Enfant");
            $this->addReference('tailleEnfant_'.$k, $taille);
            $manager->persist($taille);
        }
        $manager->flush();
    }
}

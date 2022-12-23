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
        $tabTailleNourrisson = array('2 mois','4 mois','6 mois','l an','2 ans');
        $tabTailleEnfant = array('6 ans', '8 ans', '10 ans','12 ans','14 ans');

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

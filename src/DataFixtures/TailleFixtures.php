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
        for ($i = 0; $i < 5; $i++) {
            $taille = (new Taille())
                ->setTaille($tabTaille[$i])
            ;
            $this->addReference('taille_'.$i, $taille);
            $manager->persist($taille);
        }

        $manager->flush();
    }
}

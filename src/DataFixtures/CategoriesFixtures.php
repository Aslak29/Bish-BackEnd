<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categorieFemme = (new Categorie())
            ->setName("Femme")
            ->setIsTrend(rand(0, 1))
            ->setPathImage("femme.png")
            ->setAvailable(1);
        $this->addReference('femme', $categorieFemme);


        $categorieHomme = (new Categorie())
            ->setName("Homme")
            ->setIsTrend(rand(0, 1))
            ->setPathImage("homme.png")
            ->setAvailable(1);
        $this->addReference('homme', $categorieHomme);


        $categorieFille = (new Categorie())
            ->setName("Fille")
            ->setIsTrend(1)
            ->setPathImage("fille.png")
            ->setAvailable(1);
        $this->addReference('fille', $categorieFille);


        $categorieGarcon = (new Categorie())
            ->setName("Garçon")
            ->setIsTrend(rand(0, 1))
            ->setPathImage("garcon.png")
            ->setAvailable(1);
        $this->addReference('garcon', $categorieGarcon);

        $categorieBebe = (new Categorie())
            ->setName("Bébé")
            ->setIsTrend(rand(0, 1))
            ->setPathImage("bebe.png")
            ->setAvailable(1);
        $this->addReference('bebe', $categorieBebe);

        $manager->persist($categorieFemme);
        $manager->persist($categorieHomme);
        $manager->persist($categorieFille);
        $manager->persist($categorieGarcon);
        $manager->persist($categorieBebe);



        $manager->flush();
    }
}
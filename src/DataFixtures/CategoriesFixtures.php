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
        for ($i = 1; $i < 10; $i++) {
            $categorie = (new Categorie())
                ->setName("Catégories" . $i)
                ->setIsTrend(rand(0, 1))
                ->setPathImage("image.png");
            $this->addReference('categorie_' . $i, $categorie);
            $manager->persist($categorie);
        }
        $manager->flush();
    }
}
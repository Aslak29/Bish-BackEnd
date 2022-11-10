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
            $bool = rand(0, 1);
            $categorie = (new Categorie())
                ->setName("Catégories" . $i)
                ->setIsTrend($bool);
            $this->addReference('categorie_' . $i, $categorie);
            $manager->persist($categorie);
        }
        $manager->flush();
    }
}
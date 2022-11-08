<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdresseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        /*for ($i = 1; $i < 10; $i++ ){

            $adresse = (new Adresse())
                ->setRue('rue du pont')
                ->setCity('Lille')
                ->setPostalCode(59000)
                ->setUser($this->getReference('user_'.random_int(1,6)))
            ;

            $manager->persist($adresse);
        }
        $manager->flush();*/
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}

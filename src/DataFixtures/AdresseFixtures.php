<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AdresseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; $i++ ){

            $adresse = (new Adresse())
                ->setName('Adresse '.$i)
                ->setRue('rue du pont')
                ->setNumRue('13')
                ->setCity('Lille')
                ->setPostalCode(59000)
                ->setUser($this->getReference('user_'.$i))
            ;
            $manager->persist($adresse);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}

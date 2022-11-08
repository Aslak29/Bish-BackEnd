<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; $i++ ){

            $user = (new User())
                ->setName('Demo '. $i)
                ->setFirstname('firtname'. $i)
                ->setEmail('demo'.$i.'@bish.fr')
                ->setPassword('bish'.$i)
                ->setRoles(['ROLE_USER'])
                ->setPhone(0671201001)
            ;

            $this->addReference('user_'.$i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}

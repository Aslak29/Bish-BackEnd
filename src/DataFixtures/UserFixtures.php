<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->encoder = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {

        for ($i = 1; $i < 10; $i++ ){

            $user = (new User())
                ->setName('Demo '. $i)
                ->setFirstname('firtname'. $i)
                ->setEmail('demo'.$i.'@bish.fr')
                ->setRoles(['ROLE_USER'])
                ->setPhone(0671201001)
            ;
            $user->setPassword($this->encoder->hashPassword($user, 'bish'.$i));
            $this->addReference('user_'.$i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}

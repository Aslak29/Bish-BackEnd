<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Monolog\DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->encoder = $passwordHasher;
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $dt = new DateTimeImmutable(0);
        $dt->format('Y-m-d H:i:s');

        $user = (new User())
            ->setName('admin')
            ->setFirstname('admin')
            ->setEmail('admin@bish.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPhone(0671201001)
            ->setCreatedAt($dt)
        ;
        $user->setPassword($this->encoder->hashPassword($user, 'admin'));
        $manager->persist($user);

        for ($i = 1; $i < 30; $i++ ){

            $user = (new User())
                ->setName('Demo '. $i)
                ->setFirstname('firstname'. $i)
                ->setEmail('demo'.$i.'@bish.fr')
                ->setRoles(['ROLE_USER'])
                ->setPhone(0671201001)
                ->setCreatedAt($dt)
            ;
            $user->setPassword($this->encoder->hashPassword($user, 'pass'));
            $this->addReference('user_'.$i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}

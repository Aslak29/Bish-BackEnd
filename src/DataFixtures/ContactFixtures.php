<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContactFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; $i++) {
            $contact = new Contact();
            $contact->setEmail("abc@gmail.com");
            $contact->setPhone("0102030405");
            $contact->setName("name".$i);
            $contact->setSurname("surname".$i);
            $contact->setMessage("Ceci est un message de cinquante caractères, obligatoire pour être insérer dans la base de donnée de Bish".$i);
            $contact->setUser($this->getReference('user_'.$i));
            $manager->persist($contact);
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

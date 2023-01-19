<?php

namespace App\DataFixtures;

use App\Entity\CodePromo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class CodePromoFixtures extends Fixture
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $date = date_create();
        $type = ["pourcent", "euro"];

        for ($i = 1; $i < 10; $i++) {
            $code = (new CodePromo())
                ->setName('CODEPROMO- '.$i)
                ->setRemise(random_int(10, 50))
                ->setMontantMinimum(random_int(20, 50))
                ->setStartDate(new \DateTime())
                ->setEndDate(date_date_set($date, 2023, 02, 01))
                ->setType($type[random_int(0, 1)])
            ;
            $manager->persist($code);
        }
        $manager->flush();
    }
}

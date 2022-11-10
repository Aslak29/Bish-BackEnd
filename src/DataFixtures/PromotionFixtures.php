<?php

namespace App\DataFixtures;

use App\Entity\Promotions;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PromotionFixtures extends Fixture
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 10; $i++ ){

            $end_date = new DateTime();

            $random = rand(1,60);
            $interval = new \DateInterval('P'.$random.'D');
            $promotion = new Promotions();
            $promotion->setRemise(rand(1,100));
            $promotion->setDateEnd($end_date->add($interval));
            
            $this->addReference('promotion_'.$i, $promotion);
            $manager->persist($promotion);
        }
        $manager->flush();
    }
}
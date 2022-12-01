<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $categories = array();
        for ($j = 1; $j < 10; $j++ ){
            $categories[] = $this->getReference('categorie_' . $j);
        };

        $promotions = array();
        for ($j = 1; $j < 10; $j++ ){
            $promotions[] = $this->getReference('promotion_' . $j);
        };


        for ($i = 1; $i < 100; $i++ ){

            $produit = new Produit();
            $produit->setName('Pull'. $i);
            $produit->setIsAvailable(rand(0,1));
            $produit->setIsTrend(rand(0,1));
            $produit->setPrice(random_int(1, 100));
            $produit->addCategory($categories[random_int(0,8)]);
            $produit->setPromotions($promotions[random_int(0,8)]);
            $produit->setPathImage('/image.jpg');
            $produit->setDescription('Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus, ou des touches d');

            $this->addReference('produit_'.$i, $produit);

            $manager->persist($produit);
        }

        $produits = new Produit();
        $produits->setName('Produit sans promo');
        $produits->setIsAvailable(rand(0,1));
        $produits->setIsTrend(rand(0,1));
        $produits->setPrice(random_int(1, 100));
        $produits->addCategory($categories[random_int(0,8)]);
        $produits->setPathImage('/image.jpg');
        $produits->setDescription('Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus, ou des touches d');

        $this->addReference('produit_'.$i, $produit);

        $manager->persist($produits);
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            CategoriesFixtures::class,
            PromotionFixtures::class
        ];
    }
}

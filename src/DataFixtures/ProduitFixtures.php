<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $categories[] = $this->getReference('femme');
        $categories[] = $this->getReference('homme');
        $categories[] = $this->getReference('fille');
        $categories[] = $this->getReference('garcon');
        $categories[] = $this->getReference('bebe');

        $imgProduct = [""];

        $promotions = array();
        for ($j = 1; $j < 10; $j++ ){
            $promotions[] = $this->getReference('promotion_' . $j);
        };

        // Produit avec catégorie Femme
        for ($i = 1; $i < 50; $i++ ){

            $produitFemme = new Produit();
            $produitFemme->setName('Femme '. $i);
            $produitFemme->setIsAvailable(rand(0,1));
            $produitFemme->setIsTrend(rand(0,1));
            $produitFemme->setPrice(random_int(1, 100));
            $produitFemme->addCategory($categories[0]);
            $produitFemme->setPromotions($promotions[random_int(0,4)]);
            $produitFemme->setPathImage('femme'.random_int(1,25).'.jpg');
            $produitFemme->setDescription('Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus, ou des touches d');

            $this->addReference('produit_'.$i, $produitFemme);

            $manager->persist($produitFemme);
        }

        // Produit avec catégorie Homme
        for ($a = 50; $a < 100; $a++ ){

            $produitHomme = new Produit();
            $produitHomme->setName('Homme '. $a);
            $produitHomme->setIsAvailable(rand(0,1));
            $produitHomme->setIsTrend(rand(0,1));
            $produitHomme->setPrice(random_int(1, 100));
            $produitHomme->addCategory($categories[1]);
            $produitHomme->setPromotions($promotions[random_int(0,4)]);
            $produitHomme->setPathImage('homme'.random_int(1,25).'.jpg');
            $produitHomme->setDescription('Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus, ou des touches d');

            $this->addReference('produit_'.$a, $produitHomme);

            $manager->persist($produitHomme);
        }

        // Produit avec catégorie Fille
        for ($z = 100; $z < 150; $z++ ){

            $produitFille = new Produit();
            $produitFille->setName('Fille '. $z);
            $produitFille->setIsAvailable(rand(0,1));
            $produitFille->setIsTrend(rand(0,1));
            $produitFille->setPrice(random_int(1, 100));
            $produitFille->addCategory($categories[2]);
            $produitFille->setPromotions($promotions[random_int(0,4)]);
            $produitFille->setPathImage('fille'.random_int(1,25).'.jpg');
            $produitFille->setDescription('Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus, ou des touches d');

            $this->addReference('produit_'.$z, $produitFille);

            $manager->persist($produitFille);
        }
        // Produit avec catégorie Garcon
        for ($r = 150; $r < 200; $r++ ){

            $produitGarcon = new Produit();
            $produitGarcon->setName('Garcon '. $r);
            $produitGarcon->setIsAvailable(rand(0,1));
            $produitGarcon->setIsTrend(rand(0,1));
            $produitGarcon->setPrice(random_int(1, 100));
            $produitGarcon->addCategory($categories[3]);
            $produitGarcon->setPromotions($promotions[random_int(0,4)]);
            $produitGarcon->setPathImage('garcon'.random_int(1,25).'.jpg');
            $produitGarcon->setDescription('Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus, ou des touches d');

            $this->addReference('produit_'.$r, $produitGarcon);

            $manager->persist($produitGarcon);
        }
        // Produit avec catégorie Bebe
        for ($y = 200; $y < 250; $y++ ){

            $produitBebe = new Produit();
            $produitBebe->setName('Bebe'. $y);
            $produitBebe->setIsAvailable(rand(0,1));
            $produitBebe->setIsTrend(rand(0,1));
            $produitBebe->setPrice(random_int(1, 100));
            $produitBebe->addCategory($categories[4]);
            $produitBebe->setPromotions($promotions[random_int(0,4)]);
            $produitBebe->setPathImage('bebe'.random_int(1,25).'.jpg');
            $produitBebe->setDescription('Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus, ou des touches d');

            $this->addReference('produit_'.$y, $produitBebe);

            $manager->persist($produitBebe);
        }

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


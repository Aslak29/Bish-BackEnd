<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 10; $i++ ){

            $produit = new Produit();
            $produit->setName('Pull'. $i);
            $produit->setIsAvailable(rand(0,1));
            $produit->setIsTrend(rand(0,1));
            $produit->setPrice(random_int(1, 100));
            $produit->setPathImage('/image.jpg');
            $produit->setDescription('Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus, ou des touches d');
            $produit->addCategory($this->getReference('categorie_'.$i));

            $manager->persist($produit);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            CategoriesFixtures::class
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 31; $i++) {
            $categorie = (new Blog())
                ->setTitle("Blog" . $i)
                ->setDescription("Plusieurs variations de Lorem Ipsum peuvent être trouvées ici ou là, mais la majeure partie d entre elles a été altérée par l addition d humour ou de mots aléatoires qui ne ressemblent pas une seconde à du texte standard Si vous voulez utiliser un passage du Lorem Ipsum, vous devez être sûr qu il n y a rien dembarrassant caché dans le texte. Tous les générateurs de Lorem Ipsum sur Internet tendent à reproduire le même extrait sans fin, ce qui fait de lipsum.com le seul vrai générateur de Lorem Ipsum. Iil utilise un dictionnaire de plus de 200 mots latins, en combinaison de plusieurs structures de phrases, pour générer un Lorem Ipsum irréprochable. Le Lorem Ipsum ainsi obtenu ne contient aucune répétition, ni ne contient des mots farfelus.")
                ->setPathImage('image'.random_int(1,30).'.jpg')
            ;
            $manager->persist($categorie);
        }
        $manager->flush();
    }
}
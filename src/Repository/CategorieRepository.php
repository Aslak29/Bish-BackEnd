<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function save(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function getCategorieIsTrend()
    {
        return $this->createQueryBuilder('c')
                    ->where("c.isTrend = 1")
                    ->getQuery()
                    ->getResult();
    }

    public function findOneById($idCategorie)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.id = :idCategorie')
            ->setParameters([
                "idCategorie" => $idCategorie
            ])
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllCategorieByIdProduit($idProduit)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.produits', 'p')
            ->where('p.id = :idProduit')
            ->setParameters([
                "idProduit" => $idProduit
            ])
            ->getQuery()
            ->getResult()
            ;
    }
}

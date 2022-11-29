<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Produit[] Returns an array of Produit objects
    */
   public function findByFilter($orderby,$moyenne,$minprice,$maxprice): array
   {
    $entityManager = $this->getEntityManager();
    $querySQL =
        'SELECT p
        FROM App\Entity\Produit p
        WHERE p.price BETWEEN :minprice AND :maxprice';

    if ($orderby == "ASC"){
        $querySQL .= " order by p.price ASC";
    }else if ($orderby == "DESC"){
        $querySQL .= " order by p.price DESC";
    }

    $query = $this->getEntityManager()->createQuery($querySQL);

    $query->setParameters([
        'minprice'=>$minprice,
        'maxprice'=>$maxprice
    ]);


    return $query->getResult();
   }

}

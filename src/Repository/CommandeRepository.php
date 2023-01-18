<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function save(Commande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findOneById()
    {
        return $this->createQueryBuilder("c")
                    ->where('c.id = :orderID')
                    ->getQuery()
                    ->getResult();
    }
//    /**
//     * @return Commande[] Returns an array of Commande objects
//     */
   public function findByUserId($idUser): array
   {
       return $this->createQueryBuilder('c')
           ->where('c.user = :idUser')
           ->setParameter('idUser', $idUser)
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function countMonth($startDate, $endDate) {
    return $this->createQueryBuilder('c')
        ->select("count(c.id)")
        ->where ("c.dateFacture >= :startDate and c.dateFacture <= :endDate") 
        ->setParameters([
            "startDate" => $startDate,
            "endDate" => $endDate
        ])
        ->getQuery()
        ->getResult();
}

public function recentCommande()
{
    return $this->createQueryBuilder('c')
        ->OrderBy('c.dateFacture',"DESC")
        ->setMaxResults(15)
        ->getQuery()
        ->getResult();
}
}


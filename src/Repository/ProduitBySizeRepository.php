<?php

namespace App\Repository;

use App\Entity\ProduitBySize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitBySize>
 *
 * @method ProduitBySize|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitBySize|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitBySize[]    findAll()
 * @method ProduitBySize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitBySizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitBySize::class);
    }

    public function save(ProduitBySize $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProduitBySize $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllStockByIdProduct($idProduct){

        return $this->createQueryBuilder('p')
            ->join('p.taille', 't')
            ->addSelect('t')
            ->where('p.produit = :idProduct')
            ->setParameters([
                "idProduct" => $idProduct
            ])
            ->getQuery()
            ->getResult()
        ;
    }
//    /**
//     * @return ProduitBySize[] Returns an array of ProduitBySize objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProduitBySize
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

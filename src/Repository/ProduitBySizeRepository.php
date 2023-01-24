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

    public function findByIdProductAndSize($productId, $sizeId){
        return $this->createQueryBuilder('ps')
            ->where('ps.produit = :productId')
            ->andWhere('ps.taille = :sizeId')
            ->setParameters([
                "productId" => $productId,
                "sizeId" => $sizeId
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}

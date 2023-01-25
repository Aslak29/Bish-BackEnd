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

    // SELECT * FROM produit
    // INNER JOIN categorie_produit ON produit.id = categorie_produit.produit_id
    // INNER JOIN categorie ON categorie.id = categorie_produit.categorie_id
    // WHERE categorie.id = 3
    // AND produit.id = 2;

    public function findAllProductsByIdCateg($idCateg, $id)
    {
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->where('c.id = :idCateg')
            ->join('p.produitBySize', 'ps')
            ->join('ps.taille', 't')
            ->leftJoin('p.Note', 'pn')
            ->addSelect('avg(pn.note)')
            ->andWhere('p.id != :id')
            ->andWhere('p.isAvailable = 1')
            ->setParameters([
                "idCateg" => $idCateg,
                "id" => $id
            ])
            ->groupBy('p.id')
            ->getQuery()
            ->getResult()
        ;
    }

   public function findOneById($idProduit)
   {
       return $this->createQueryBuilder('p')
           ->leftJoin('p.categories', 'c')
           ->addSelect('c')
           ->leftJoin('p.Note', 'pn')
           ->addSelect('avg(pn.note)')
           ->where('p.id = :idProduit')
           ->andWhere('p.isAvailable = 1')
           ->setParameters([
               "idProduit" => $idProduit
           ])
           ->groupBy('p.id')
           ->getQuery()
           ->getResult()
           ;
   }

    public function findOneByIdForDelete($idProduit)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->leftJoin('p.Note', 'pn')
            ->addSelect('avg(pn.note)')
            ->where('p.id = :idProduit')
            ->setParameters([
                "idProduit" => $idProduit
            ])
            ->groupBy('p.id')
            ->getQuery()
            ->getResult()
            ;
    }
   /**
    * @return Produit[] Returns an array of Produit objects
    */
   public function findByFilter($orderby,$moyenne,$minprice,$maxprice,$idCategorie,$size,$limit,$offset): array
   {
       $qb = $this->createQueryBuilder('p')
           ->leftJoin('p.Note', 'pn')
           ->addSelect('avg(pn.note)')
           ->leftJoin('p.produitBySize', 'ps')
           ->leftJoin('ps.taille', 't')
           ->where('p.price between :minprice AND :maxprice')
           ->andWhere("p.isAvailable = 1");
            

       if ($idCategorie !== "-1") {
           $qb->leftJoin('p.categories', 'c');
           $qb->andWhere('c.id = :idCategorie');
       }

       if ($size !== "none"){
           $qb->andWhere('t.taille = :size AND ps.stock > 0');
       }

       if ($orderby == "ASC") {
           $qb->orderBy('p.price', 'ASC');
       } elseif ($orderby == "DESC") {
           $qb->orderBy('p.price', 'DESC');
       }

       $qb->setMaxResults($limit);
       $qb->setFirstResult($offset);

       if ($idCategorie !== "-1") {
           $qb->setParameters([
               'minprice' => $minprice,
               'maxprice' => $maxprice,
               'idCategorie' => $idCategorie,
           ]);
           if ($size !== "none") {
               $qb->setParameter('size', $size);
           }
       } else {
           $qb->setParameters([
               'minprice' => $minprice,
               'maxprice' => $maxprice,
           ]);
           if ($size !== "none"){
               $qb->setParameter('size', $size);
           }
       }
       $qb->groupBy('p.id');
       return $qb->getQuery()->getResult();
   }
    public function getProduitIsTrend()
    {
        return $this->createQueryBuilder('p')
                    ->where("p.isTrend = 1")
                    ->andWhere("p.isAvailable = 1")
                    ->getQuery()
                    ->getResult();
    }
    public function findByBestPromo()
    {
        return $this->createQueryBuilder('p')
                    ->join('p.promotions', 'promo')
                    ->where("p.promotions= promo.id")
                    ->andWhere("p.isAvailable = 1")
                    ->orderBy("promo.remise", "DESC")
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult();
    }
       /**
        * @return Produit[] Returns an array of Produit objects
        */
       public function countByFilter($orderby,$moyenne,$minprice,$maxprice,$idCategorie,$size): array
   {
       $qb = $this->createQueryBuilder('p')
           ->select('count(DISTINCT p.id)')
           ->where('p.price between :minprice AND :maxprice')
           ->andWhere("p.isAvailable = 1")
           ->leftJoin('p.produitBySize', 'ps')
           ->leftJoin('ps.taille', 't');

       if ($idCategorie !== "-1") {
           $qb->leftJoin('p.categories', 'c');
           $qb->andWhere('c.id = :idCategorie');
       }
       if ($size !== "none") {
           $qb->andWhere('t.taille = :size AND ps.stock > 0');
       }

       if ($orderby == "ASC") {
           $qb->orderBy('p.price', 'ASC');
       } elseif ($orderby == "DESC") {
           $qb->orderBy('p.price', 'DESC');
       }

       if ($idCategorie !== "-1") {
           $qb->setParameters([
               'minprice' => $minprice,
               'maxprice' => $maxprice,
               'idCategorie' => $idCategorie
           ]);
           if ($size !== "none") {
               $qb->setParameter('size', $size);
           }
       } else {
           $qb->setParameters([
               'minprice' => $minprice,
               'maxprice' => $maxprice,
           ]);
           if ($size !== "none"){
               $qb->setParameter('size', $size);
           }
       }

    return $qb->getQuery()->getResult();
   }

    public function findProductPromo()
    {
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->join('p.promotions', 'pp')
            ->where("p.isAvailable = 1")
            ->addSelect('c,pp')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findProductByIdPromo($idPromo)
    {
           return $this->createQueryBuilder('p')
               ->where('p.promotions = :idPromotion')
               ->setParameters([
                   "idPromotion" => $idPromo
               ])
               ->getQuery()
               ->getResult();
    }
    
    public function countAll():array {
        return $this->createQueryBuilder('p')
            ->select("COUNT(p.id)")
            ->getQuery()
            ->getResult();
 }

    public function likeName($name):array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name',  '%'.$name.'%')
            ->getQuery()
            ->getResult();
    }
}

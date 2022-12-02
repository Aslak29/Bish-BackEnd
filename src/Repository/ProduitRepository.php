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

    public function findAllProductsByIdCateg($idCateg, $id){
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->where('c.id = :idCateg')
            ->join('p.produitBySize', 'ps')
            ->addSelect('ps')
            ->join('ps.taille','t')
            ->addSelect('t')
            ->andWhere('p.id != :id')
            ->setParameters([
                "idCateg" => $idCateg,
                "id" => $id
            ])
            ->getQuery()
            ->getResult()
        ;
    }

   public function findOneById($idProduit)
   {
       return $this->createQueryBuilder('p')
           ->leftJoin('p.categories', 'c')
           ->addSelect('c')
           ->where('p.id = :idProduit')
           ->setParameters([
               "idProduit" => $idProduit
           ])
           ->getQuery()
           ->getResult()
           ;
   }
   /**
    * @return Produit[] Returns an array of Produit objects
    */
   public function findByFilter($orderby,$moyenne,$minprice,$maxprice,$idCategorie,$limit,$offset): array
   {
       $qb = $this->createQueryBuilder('p')
           ->leftJoin('p.produitBySize', 'ps')
           ->addSelect('ps')
           ->leftJoin('p.Note', 'pn')
            ->addSelect('AVG(pn.note)')
           ->leftJoin('ps.taille', 't')
           ->addSelect('t')
           ->where('p.price between :minprice AND :maxprice');
            

       if ($idCategorie !== "-1") {
           $qb->leftJoin('p.categories', 'c');
           $qb->andWhere('c.id = :idCategorie');
       }

       if ($orderby == "ASC") {
           $qb->orderBy('p.price', 'ASC');
       } else if ($orderby == "DESC") {
           $qb->orderBy('p.price', 'DESC');
       }

       $qb->setMaxResults($limit);
       $qb->setFirstResult($offset);

       if ($idCategorie !== "-1") {
           $qb->setParameters([
               'minprice' => $minprice,
               'maxprice' => $maxprice,
               'idCategorie' => $idCategorie
           ]);
       } else {
           $qb->setParameters([
               'minprice' => $minprice,
               'maxprice' => $maxprice,
           ]);
       }
       return $qb->getQuery()->getResult();
   }

       /**
        * @return Produit[] Returns an array of Produit objects
        */
       public function countByFilter($orderby,$moyenne,$minprice,$maxprice,$idCategorie): array
   {
       $qb = $this->createQueryBuilder('p')
           ->select('count(p)')
           ->where('p.price between :minprice AND :maxprice');

       if ($idCategorie !== "-1") {
           $qb->leftJoin('p.categories', 'c');
           $qb->andWhere('c.id = :idCategorie');
       }

       if ($orderby == "ASC") {
           $qb->orderBy('p.price', 'ASC');
       } else if ($orderby == "DESC") {
           $qb->orderBy('p.price', 'DESC');
       }

       if ($idCategorie !== "-1") {
           $qb->setParameters([
               'minprice' => $minprice,
               'maxprice' => $maxprice,
               'idCategorie' => $idCategorie
           ]);
       } else {
           $qb->setParameters([
               'minprice' => $minprice,
               'maxprice' => $maxprice,
           ]);
       }

    return $qb->getQuery()->getResult();
   }

    public function findProductPromo(){
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->join('p.promotions', 'pp')
            ->addSelect('c,pp')
            ->getQuery()
            ->getResult()
            ;
    }

}

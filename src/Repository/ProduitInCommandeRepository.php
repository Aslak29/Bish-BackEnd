<?php

namespace App\Repository;

use App\Entity\ProduitInCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitInCommande>
 *
 * @method ProduitInCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitInCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitInCommande[]    findAll()
 * @method ProduitInCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitInCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitInCommande::class);
    }

    public function save(ProduitInCommande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProduitInCommande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneOrderbyIdCommandes($idCommande)
    {
        return $this->createQueryBuilder('pc')
            ->leftJoin( 'pc.commandes', 'c')
            ->where('c.id = :idCommande')
            -> leftJoin('pc.produits', 'p')
            ->setParameters([
                "idCommande" => $idCommande
            ])
            ->getQuery()
            ->getResult();
    }

}

<?php

namespace App\Repository;

use App\Entity\Notation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notation>
 *
 * @method Notation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notation[]    findAll()
 * @method Notation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notation::class);
    }

    public function save(Notation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Notation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findNote(Notation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->find($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findNoteByUser($userId, $productId)
    {
        return $this->createQueryBuilder('n')
            ->join("n.user", "nu")
            ->join("n.produit", "np")
            ->andWhere("nu.id = :userId")
            ->andWhere("np.id = :productId")
            ->setParameters([
                "userId" => $userId,
                "productId" => $productId
            ])
            ->getQuery()
            ->getResult();
    }
}

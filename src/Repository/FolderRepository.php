<?php

namespace App\Repository;

use App\Entity\Folder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Folder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Folder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Folder[]    findAll()
 * @method Folder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    /**
    * @return Folder[] Returns an array of Folder objects
    */
    public function findById($id)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id = :val')
            ->setParameter('val', $id)
            ->orderBy('f.id', 'ASC')
            ->getMaxResults()
            ->getQuery()
            ->getResult()
        ;
    }



    public function findOneByName($value, $user): ?Folder
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.name = :val')
            ->andWhere('f.user = :user')
            ->setParameter('val', $value)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}

<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /**
    * @return Document[] Returns an array of Document objects
    */

    public function findById($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.folder = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByNameAndFolder($name, $folder)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.folder = :folder')
            ->andWhere('d.name = :name')
            ->setParameter('name', $name)
            ->setParameter('folder', $folder)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Document
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

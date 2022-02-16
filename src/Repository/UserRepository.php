<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function loadUserByUsername(array $username): ?User
    {
        $entityManager = $this->getEntityManager();


        return $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\User u
            WHERE u.username = :query'
        )->setParameter('query', $username['username'])->getOneOrNullResult();
    }

    public function loadUserById(int $id): ?User
    {
        $entityManager = $this->getEntityManager();


        return $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\User u
            WHERE u.id = :query'
        )->setParameter('query', $id)->getOneOrNullResult();
    }



    public function loadUserByUsernameAndPassword($credentials)
    {
        $entityManager = $this->getEntityManager();


        return $entityManager->createQuery(
            'SELECT u
            FROM App\Entity\User u
            WHERE u.username = :query
            AND u.password = :querypass'
        )->setParameters(array('query' => $credentials['username'], 'querypass' => $credentials['password']))->getOneOrNullResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

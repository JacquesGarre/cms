<?php

namespace App\Repository;

use App\Entity\FrontOfficeTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrontOfficeTemplate>
 *
 * @method FrontOfficeTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrontOfficeTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrontOfficeTemplate[]    findAll()
 * @method FrontOfficeTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrontOfficeTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrontOfficeTemplate::class);
    }

    public function add(FrontOfficeTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrontOfficeTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FrontOfficeTemplate[] Returns an array of FrontOfficeTemplate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FrontOfficeTemplate
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

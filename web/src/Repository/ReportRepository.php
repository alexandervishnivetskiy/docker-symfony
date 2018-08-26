<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Report::class);
    }


    public function findAllReportByName($name)
    {

        $queryBuilder = $this->createQueryBuilder('r')
            ->select('r')
            ->from('App\Entity\Report', 't')
            ->where("r.name LIKE :name")
            ->setParameter('name', "%$name%")
            ->getQuery();

        return $queryBuilder->execute();
    }

    public function findAllReportsByClientID($id)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select('r')
            ->from('App\Entity\Report', 't')
            ->where("r.client = :id")
            ->setParameter('id', "$id")
            ->getQuery();

        return $queryBuilder->execute();
    }
}
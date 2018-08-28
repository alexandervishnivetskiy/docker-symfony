<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findClientByName($name)
    {

        $queryBuilder = $this->createQueryBuilder('r')
            ->select('r')
            ->from('App\Entity\Client', 't')
            ->where("r.name = :name")
            ->setParameter('name', "$name")
            ->getQuery();
        $client = $queryBuilder->execute();
        return $client[0];
    }
}
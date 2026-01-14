<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function findByBusinessPartner(BusinessPartner $businessPartner): array {
        return $this->createQueryBuilder('t')
            ->andWhere('t.businessPartner = :businessPartner')
            ->setParameter('businessPartner', $businessPartner)
            ->getQuery()
            ->getResult();
    }
}

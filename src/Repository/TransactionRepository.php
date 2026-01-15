<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findByAccount(Account $account): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.account = :account')
            ->setParameter('account', $account)
            ->getQuery()
            ->getResult();
    }

    /**
     * This method displays a partner's transactions
     * filtered by a specific currency (e.g., CHF or EUR).
     */
    public function findByPartnerAndCurrency(BusinessPartner $partner, string $currency): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.account', 'a') // On joint la nouvelle entité Account
            ->where('a.businessPartner = :partner')
            ->andWhere('a.currency = :currency')
            ->setParameter('partner', $partner)
            ->setParameter('currency', $currency)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Enums\CurrencyEnum;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;

class BalanceManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountRepository $accountRepository,
    ) {
    }

    public function increaseBalance(Account $account, string $amount): string
    {
        $balance = (float)$account->getBalance();
        $balance += (float)$amount;
        $account->setBalance((string)$balance);

        $this->entityManager->flush();

        return $balance;
    }

    public function decreaseBalance(Account $account, string $amount): string
    {
        $balance = (float)$account->getBalance();
        $balance -= (float)$amount;
        $account->setBalance((string)$balance);

        $this->entityManager->flush();

        return $balance;
    }

    public function hasEnoughMoneyForPayout(Account $account, string $amount): bool
    {
        $remainingBalance = (float)$account->getBalance();
        $remainingBalance -= (float)$amount;

        return $remainingBalance >= 0;
    }

    public function getOrCreateAccount(BusinessPartner $partner, CurrencyEnum $currency): Account
    {
        $account = $this->accountRepository->findOneBy([
            'businessPartner' => $partner,
            'currency' => $currency
        ]);

        if (!$account) {
            $account = new Account();
            $account->setBusinessPartner($partner);
            $account->setCurrency($currency);
            $account->setBalance('0.00');
            $this->entityManager->persist($account);
        }

        return $account;
    }
}

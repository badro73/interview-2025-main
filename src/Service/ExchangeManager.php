<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Entity\Transaction;
use App\Enums\CurrencyEnum;
use App\Enums\TransactionTypeEnum;
use Doctrine\ORM\EntityManagerInterface;

class ExchangeManager
{
    public function __construct(
        private readonly BalanceManager $balanceManager,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function executeExchange(BusinessPartner $partner, string $from, string $to, string $amount): void
    {
        $fromEnum = CurrencyEnum::from($from);
        $toEnum = CurrencyEnum::from($to);
        $toAmount = bcmul($amount, "1.1", 2);

        $this->entityManager->beginTransaction();
        try {
            $fromAcc = $this->balanceManager->getOrCreateAccount($partner, $fromEnum);
            $toAcc = $this->balanceManager->getOrCreateAccount($partner, $toEnum);

            if (!$this->balanceManager->hasEnoughMoneyForPayout($fromAcc, $amount)) {
                throw new \Exception("Insufficient balance in $from");
            }

            $this->balanceManager->decreaseBalance($fromAcc, $amount);
            $this->balanceManager->increaseBalance($toAcc, $toAmount);

            $now = new \DateTimeImmutable();
            $this->createTx($fromAcc, $amount, "Sell $from to $to", TransactionTypeEnum::PAYOUT, $now);
            $this->createTx($toAcc, $toAmount, "Buy $to from $from", TransactionTypeEnum::PAYIN, $now);

            $this->entityManager->flush();
            $this->entityManager->commit();

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    private function createTx(Account $acc, string $amt, string $name, TransactionTypeEnum $type, \DateTimeImmutable $date): void
    {
        $tx = new Transaction();
        $tx->setAccount($acc);
        $tx->setAmount($amt);
        $tx->setName($name);
        $tx->setType($type);
        $tx->setDate($date);
        $tx->setExecuted(true);
        $tx->setCountry($acc->getBusinessPartner()->getCountry());
        $tx->setIban("INTERNAL");

        $this->entityManager->persist($tx);
    }
}

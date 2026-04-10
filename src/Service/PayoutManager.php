<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\TransactionExecutionException;
use App\Message\ExchangeMessage;
use DateTime;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PayoutManager
{
    public function __construct(
        private readonly BalanceManager $balanceManager,
        private readonly MessageBusInterface $bus
    ) {
    }

    /**
     * @throws TransactionExecutionException
     * @throws ExceptionInterface
     */
    public function execute(Transaction $transaction): void
    {
        if ($transaction->getType() !== TransactionTypeEnum::PAYOUT) {
            throw new TransactionExecutionException('Transaction type is not payout');
        }

        if ($transaction->isExecuted()) {
            throw new TransactionExecutionException('Transaction is already executed');
        }

        if ($transaction->getDate() > (new DateTime())) {
            throw new TransactionExecutionException('Payout transaction date can be only on the current date');
        }

        if (!$this->balanceManager->hasEnoughMoneyForPayout(
            $transaction->getAccount(),
            $transaction->getAmount()
        )) {
            throw new TransactionExecutionException('You do not have enough money for a payout');
        }

        $message = new ExchangeMessage($transaction->getId());

        try {
           $this->bus->dispatch($message);
        }catch (\Exception $exception){
            throw new TransactionExecutionException($exception->getMessage());
        }
    }
}

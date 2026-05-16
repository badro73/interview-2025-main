<?php

namespace App\MessageHandler;

use App\Message\ExchangeMessage;
use App\Repository\TransactionRepository;
use App\Service\BalanceManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExchangeMessageHandler
{

    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly BalanceManager $balanceManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExchangeMessage $message): void
    {
        $data = $message->getTransactionId();

        $transaction = $this->transactionRepository->findOneBy(['id' => $data]);
        $transaction->setExecuted(true);

        $this->balanceManager->decreaseBalance($transaction->getAccount(), $transaction->getAmount());

        $this->logger->info('transaction has been executed');
    }
}
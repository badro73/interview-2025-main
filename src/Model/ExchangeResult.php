<?php

namespace App\Model;

use App\Entity\Account;
use App\Entity\Transaction;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class ExchangeResult
{
    public function __construct(
        public Account $fromAccount,
        public Account $toAccount,
        public Transaction $sellTransaction,
        public Transaction $buyTransaction,
        public string $rate,
        public string $boughtAmount
    ) {}
}

<?php

namespace App\Message;

class ExchangeMessage
{
    public function __construct(
        private readonly int $transactionId,
    ) {}

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
}
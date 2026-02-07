<?php

namespace App\Dto;

class ExchangeOutput
{
    public function __construct(
        public string $fromCurrency,
        public string $toCurrency,
        public string $amountSold,
        public string $amountBought,
        public string $newBalanceFrom,
        public string $newBalanceTo,
    ) {}
}

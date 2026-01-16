<?php

namespace App\Model;

use App\Entity\BusinessPartner;
use App\Enums\CurrencyEnum;

class Exchange
{
    public function __construct(
         public CurrencyEnum $fromCurrency = CurrencyEnum::CHF,
         public CurrencyEnum $toCurrency = CurrencyEnum::CHF,
         public string $amount = '0',
         public ?BusinessPartner $businessPartner = null
    ) {}
}

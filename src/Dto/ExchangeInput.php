<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ExchangeInput
{
    public function __construct(
        #[Assert\NotBlank] public string $fromCurrency,
        #[Assert\NotBlank] public string $toCurrency,
        #[Assert\Positive] public string $amount,
        #[Assert\NotBlank] public int $businessPartnerId
    ) {}
}

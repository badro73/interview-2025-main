<?php

namespace App\Dto;

use App\Enums\CurrencyEnum;
use Symfony\Component\Validator\Constraints as Assert;

class ExchangeInput
{
    public function __construct(
        #[Assert\NotBlank(message: "The source currency is required.")]
        #[Assert\Type(type: CurrencyEnum::class, message: "Invalid source currency.")]
        public ?CurrencyEnum $fromCurrency = null,

        #[Assert\NotBlank(message: "The destination currency is required.")]
        #[Assert\Type(type: CurrencyEnum::class, message: "Invalid destination currency.")]
        public ?CurrencyEnum $toCurrency = null,

        #[Assert\NotBlank(message: "The amount is required.")]
        #[Assert\Positive(message: "The amount must be a positive number.")]
        public string $amount = '0',

        #[Assert\NotBlank(message: "The business partner ID is required.")]
        #[Assert\Type(type: 'integer', message: "The business partner ID must be an integer.")]
        public ?int $businessPartnerId = null
    ) {}
}

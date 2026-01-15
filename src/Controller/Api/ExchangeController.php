<?php

namespace App\Controller\Api;

use App\Dto\ExchangeInput;
use App\Dto\ExchangeOutput;
use App\Entity\Transaction;
use App\Repository\BusinessPartnerRepository;
use App\Service\ExchangeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class ExchangeController extends AbstractController
{
    public function __construct(
        private readonly ExchangeManager $exchangeManager,
        private readonly BusinessPartnerRepository $businessPartnerRepository
    ) {}

    /**
     * @param ExchangeInput $exchangeInput
     * @return Transaction[]
     */
    public function __invoke(#[MapRequestPayload] ExchangeInput $exchangeInput): array
    {
        $partner = $this->businessPartnerRepository->find($exchangeInput->businessPartnerId);

        if (!$partner) {
            throw new BadRequestHttpException("Business Partner not found.");
        }

        try {
            $result = $this->exchangeManager->executeExchange(
                $partner,
                $exchangeInput->fromCurrency,
                $exchangeInput->toCurrency,
                $exchangeInput->amount
            );

        return [$result->buyTransaction, $result->sellTransaction];

        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}

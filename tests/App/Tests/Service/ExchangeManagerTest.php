<?php

namespace App\Tests\Service;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Enums\CurrencyEnum;
use App\Model\ExchangeResult;
use App\Service\BalanceManager;
use App\Service\ExchangeManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ExchangeManagerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testExecuteExchangeSuccess(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $balanceManager = $this->createMock(BalanceManager::class);

        $partner = new BusinessPartner();
        $partner->setCountry('CH');

        $chfAccount = new Account();
        $chfAccount->setBalance('1000.00');
        $chfAccount->setCurrency(CurrencyEnum::CHF);
        $chfAccount->setBusinessPartner($partner);

        $eurAccount = new Account();
        $eurAccount->setBalance('0.00');
        $eurAccount->setCurrency(CurrencyEnum::EUR);
        $eurAccount->setBusinessPartner($partner);

        $balanceManager->method('getOrCreateAccount')
            ->willReturnMap([
                [$partner, CurrencyEnum::CHF, $chfAccount],
                [$partner, CurrencyEnum::EUR, $eurAccount],
            ]);

        $balanceManager->method('hasEnoughMoneyForPayout')->willReturn(true);

        $entityManager->expects($this->once())->method('beginTransaction');
        $entityManager->expects($this->once())->method('commit');

        $manager = new ExchangeManager($balanceManager, $entityManager);
        $result = $manager->executeExchange($partner, CurrencyEnum::CHF, CurrencyEnum::EUR, '1000');

        $this->assertInstanceOf(ExchangeResult::class, $result);
        $this->assertEquals('1100.00', $result->boughtAmount);
        $this->assertEquals('1100.00', $result->boughtAmount);
        $this->assertEquals('1.1', $result->rate);
    }
}

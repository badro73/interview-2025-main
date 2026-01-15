<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Enums\BusinessPartnerStatusEnum;
use App\Enums\CurrencyEnum;
use App\Enums\LegalFormEnum;
use App\Service\BalanceManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BalanceManagerTest extends WebTestCase
{
    private BalanceManager $balanceManager;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();

        $this->balanceManager = $container->get(BalanceManager::class);
    }

    public function testPayinBalanceChange(): void
    {
        $account = $this->createAccount('10000.00', CurrencyEnum::CHF);

        $this->balanceManager->increaseBalance($account, '1000.00');

        $this->assertSame(
            0,
            bccomp('11000.00', $account->getBalance(), 2),
            sprintf('Balance should be 11000.00, but got: %s', $account->getBalance())
        );
    }

    public function testPayoutBalanceChange(): void
    {
        $account = $this->createAccount('10000.00', CurrencyEnum::CHF);

        $this->balanceManager->decreaseBalance($account, '1000');

        $this->assertSame(
            0,
            bccomp('9000.00', $account->getBalance(), 2),
            sprintf('Expected 9000.00, got %s', $account->getBalance())
        );
    }

    public function testHasEnoughMoneyForPayout(): void
    {
        $account = $this->createAccount('1000.00', CurrencyEnum::CHF);

        $this->assertTrue($this->balanceManager->hasEnoughMoneyForPayout($account, '1000.00'));
        $this->assertFalse($this->balanceManager->hasEnoughMoneyForPayout($account, '1000.01'));
    }

    public function testDecreaseBalance(): void
    {
        $account = $this->createAccount('1000.00', CurrencyEnum::CHF);

        $this->balanceManager->decreaseBalance($account, '400.00');

        $this->assertEquals('600.00', number_format((float)$account->getBalance(), 2, '.', ''));
    }

    private function createAccount(string $balance, CurrencyEnum $currency): Account
    {
        $partner = new BusinessPartner();
        $partner->setName('Test Partner');
        $partner->setStatus(BusinessPartnerStatusEnum::ACTIVE);
        $partner->setLegalForm(LegalFormEnum::LIMITED_LIABILITY_COMPANY);
        $partner->setCountry('CH');

        $account = new Account();
        $account->setBusinessPartner($partner);
        $account->setCurrency($currency);
        $account->setBalance($balance);

        return $account;
    }
}

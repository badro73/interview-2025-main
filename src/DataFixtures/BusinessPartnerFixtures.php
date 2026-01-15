<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Enums\BusinessPartnerStatusEnum;
use App\Enums\CurrencyEnum;
use App\Enums\LegalFormEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BusinessPartnerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $partner1 = new BusinessPartner();
        $partner1->setName('AMNIS Treasury Services AG');
        $partner1->setStatus(BusinessPartnerStatusEnum::ACTIVE);
        $partner1->setLegalForm(LegalFormEnum::LIMITED_LIABILITY_COMPANY);
        $partner1->setAddress('Baslerstrasse 60');
        $partner1->setCity('Zürich');
        $partner1->setZip(8048);
        $partner1->setCountry('CH');

        $manager->persist($partner1);

        // Ajout d'un compte CHF pour le premier partenaire
        $account1CHF = new Account();
        $account1CHF->setCurrency(CurrencyEnum::CHF);
        $account1CHF->setBalance('0.00');
        $account1CHF->setBusinessPartner($partner1);
        $manager->persist($account1CHF);

        $partner2 = new BusinessPartner();
        $partner2->setName('AMNIS Europe AG');
        $partner2->setStatus(BusinessPartnerStatusEnum::INACTIVE);
        $partner2->setLegalForm(LegalFormEnum::LIMITED_LIABILITY_COMPANY);
        $partner2->setAddress('Gewerbeweg 15');
        $partner2->setCity('Vaduz');
        $partner2->setZip(9490);
        $partner2->setCountry('LI');

        $manager->persist($partner2);

        $account = new Account();
        $account->setCurrency(CurrencyEnum::CHF);
        $account->setBalance('0.00');
        $account->setBusinessPartner($partner1);
        $manager->persist($account);

        $manager->flush();
    }
}

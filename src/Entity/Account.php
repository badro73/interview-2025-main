<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enums\CurrencyEnum;
use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'accounts')]
#[ApiResource(
    operations: [
        new Get(),
        new Patch(),
        new Post(
            denormalizationContext: ['groups' => ['AccountCreate']]
        ),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['AccountView']]
)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['AccountView', 'TransactionView'])]
    private ?int $id = null;

    #[ORM\Column(enumType: CurrencyEnum::class)]
    #[Groups(['AccountView', 'AccountCreate', 'TransactionView'])]
    private CurrencyEnum $currency = CurrencyEnum::CHF;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(0)]
    #[Groups(['AccountView', 'AccountCreate', 'TransactionView'])]
    private string $balance = '0';

    #[ORM\ManyToOne(targetEntity: BusinessPartner::class, inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['AccountView', 'AccountCreate'])]
    private BusinessPartner $businessPartner;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'account')]
    #[Groups(['AccountView'])]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrency(): CurrencyEnum
    {
        return $this->currency;
    }

    public function setCurrency(CurrencyEnum $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getBalance(): string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getBusinessPartner(): BusinessPartner
    {
        return $this->businessPartner;
    }

    public function setBusinessPartner(BusinessPartner $businessPartner): static
    {
        $this->businessPartner = $businessPartner;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}

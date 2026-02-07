<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Enums\CurrencyEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency', EnumType::class, ['class' => CurrencyEnum::class])
            ->add('balance')
            ->add('businessPartner', EntityType::class, [
                'class' => BusinessPartner::class,
                'choice_label' => function (BusinessPartner $businessPartner) {
                    return sprintf(
                        '%s - %s (Client: %s)',
                        $businessPartner->getId(),
                        $businessPartner->getName(),
                        $businessPartner->getCountry()
                    );
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }

}

<?php

namespace App\Form;

use App\Dto\ExchangeInput;
use App\Entity\BusinessPartner;
use App\Enums\CurrencyEnum;
use App\Model\Exchange;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExchangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('businessPartner', EntityType::class, [
                'class' => BusinessPartner::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'label' => 'Business Partner'
            ])
            ->add('fromCurrency', EnumType::class, [
                'class' => CurrencyEnum::class,
                'label' => 'From Currency'
            ])
            ->add('toCurrency', EnumType::class, [
                'class' => CurrencyEnum::class,
                'label' => 'To Currency'
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Amount to Exchange',
                'html5' => true,
                'attr' => ['step' => '0.01']
            ])
            ->add('finalAmount', NumberType::class, [
                'label' => 'Estimated Amount to Receive',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control bg-light',
                    'placeholder' => '0.00'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exchange::class,
        ]);
    }
}

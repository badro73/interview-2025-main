<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Enums\TransactionTypeEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount')
            ->add('name')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('type', EnumType::class, ["class" => TransactionTypeEnum::class])
            ->add('country')
            ->add('iban')
            ->add('account', EntityType::class, [
                'class' => Account::class,
                'choice_label' => function (Account $account) {
                    return sprintf(
                        '%s - %s (balance: %s)',
                        $account->getBusinessPartner()->getName(),
                        $account->getCurrency()->value,
                        $account->getBalance()
                    );
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}

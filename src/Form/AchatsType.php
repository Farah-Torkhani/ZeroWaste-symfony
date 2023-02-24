<?php

namespace App\Form;

use App\Entity\Achats;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AchatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('dateAchat')
            ->add('FullName')
            ->add('Email')
            ->add('Address')
            ->add('tel')
            ->add('city')
            ->add('zipCode')
            //->add('paymentMethod')
            //->add('validate')
            //->add('commande')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achats::class,
        ]);
    }
}

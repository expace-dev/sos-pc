<?php

namespace App\Form\Booking\Client;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('beginAt', HiddenType::class)
        ->add('endAt', HiddenType::class)
        ->add('title', ChoiceType::class, [
            'choices' => [
                'Type de dépannage' => '',
                'Dépannage à distance' => 'Dépannage à distance',
                'Dépannage à domicile' => 'Dépannage à domicile'
            ],
            'attr' => [
                'class' => 'mb-3 mt-3'
            ]
        ])
        ->add('description', TextareaType::class, [
            'attr' => [
                'rows' => '10',
                'placeholder' => 'Description du problème',
                'class' => 'mb-3'
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}

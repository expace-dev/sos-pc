<?php

namespace App\Form\Compta\Admin;

use App\Entity\Users;
use App\Entity\Factures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FacturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'fullName',
                'label' => 'Client',
                'placeholder' => 'Sélectionnez un client'
            ])
            ->add('titre', ChoiceType::class, [
                'choices' => [
                    'Dépannage à distance' => 'Dépannage à distance',
                    'Dépannage à domicile' => 'Dépannage à domicile'
                ],
                'label' => 'Type d\'intervention',
                'placeholder' => 'Sélectionnez une catégorie',
            ])
            ->add('contenu', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description',
                    'rows' => '6'
                ],
                'label' => 'Description'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Factures::class,
        ]);
    }
}

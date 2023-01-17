<?php

namespace App\Form\Blog\Admin;

use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Catégorie',
                'attr' => [
                    'placeholder' => 'Catégorie'
                ]
            ])
            ->add('parent', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie parente',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
        ]);
    }
}

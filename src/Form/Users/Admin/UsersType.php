<?php

namespace App\Form\Users\Admin;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Email'
                ],
                'label' => 'Email'
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Activer le compte',
                'required' => false
            ])
            ->add('avatar', FileType::class, [
                'attr' => [
                    'is' => 'drop-files',
                    'label' => 'Déposez votre photo de profil ou cliquez pour ajouter.',
                    'help' => 'Seul les fichiers jpg jpeg et png sont accepté',
                ],
                'label' => false,
                'multiple' => false,
                'data_class' => null,
                'mapped' => false,
                'required' => false,
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom'
                ],
                'label' => 'Nom'
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'placeholder' => 'Prénom'
                ],
                'label' => 'Prénom'
            ])
            ->add('adresse', TextType::class, [
                'attr' => [
                    'placeholder' => 'Adresse'
                ],
                'label' => 'Adresse'
            ])
            ->add('codePostal', HiddenType::class)
            ->add('ville', HiddenType::class)
            ->add('pays', HiddenType::class)
            ->add('societe', TextType::class, [
                'attr' => [
                    'placeholder' => 'Société'
                ],
                'label' => 'Société'
            ])
            ->add('telephone', TelType::class, [
                'attr' => [
                    'placeholder' => 'Téléphone'
                ],
                'label' => 'Téléphone'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}

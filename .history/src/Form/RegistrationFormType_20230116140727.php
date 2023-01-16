<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions générales',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions générales',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez choisir un mot de passe'
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Mot de passe trop court, minimum {{ limit }} caractères',
                            'max' => 30,
                            'maxMessage' => 'Mot de passe trop long, maximum {{ limit }} caractères'

                        ])
                    ],
                    'required' => 'true',
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Mot de passe',
                        'autocomplete' => 'off',
                    ]
                ],
                'second_options' => [
                    'required' => 'true',
                    'label' => 'Confirmation',
                    'attr' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Confirmation'
                    ]
                ],
                'invalid_message' => 'Les mot de passe ne correspondent pas',
                'mapped' => false
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

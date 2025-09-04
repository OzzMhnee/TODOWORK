<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstName', null, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre prénom',
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'mapped' => false,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'password-input',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Le mot de passe doit contenir au moins une majuscule',
                        ]),
                        new Regex([
                            'pattern' => '/[a-z]/',
                            'message' => 'Le mot de passe doit contenir au moins une minuscule',
                        ]),
                        new Regex([
                            'pattern' => '/\d/',
                            'message' => 'Le mot de passe doit contenir au moins un chiffre',
                        ]),
                        new Regex([
                            'pattern' => '/[\W_]/',
                            'message' => 'Le mot de passe doit contenir au moins un caractère spécial',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'password-input',
                    ],
                ],
            ]);
                if (empty($options['edit_profile'])) {
            $builder->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => false, // On gère le label dans le template Twig
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation.',
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'edit_profile' => false,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'input-field',
                    'minlength' => '2',
                    'maxlength' => '180',
                    'placeholder' => 'Adresse email' 
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(['min' => 2, 'max' => 180])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'input-field']],
                'required' => true,
                'first_options'  => [
                    'attr' => [
                        'placeholder' => 'Mot de passe',
                        'class' => 'input-field'
                    ],
                    'label' => false
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Confirmez',
                        'class' => 'input-field'
                    ],
                    'label' => false
                ],
            ])
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'input-field',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Prénom'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'class' => 'input-field',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Nom'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 50])
                ]
            ])
            ->add('mobile', TextType::class, [
                'attr' => [
                    'class' => 'input-field',
                    'minlength' => '2',
                    'maxlength' => '20',
                    'placeholder' => 'Téléphone'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 2, 'max' => 20])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'submit',
                    'value' => 'Inscription'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
<?php

namespace App\Form;

use App\Entity\Commercial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommercialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstName', TextType::class, [
            'attr' => [
                'class' => 'form-control mt-2',
                'minlength' => '2',
                'maxlength' => '50',
                'placeholder' => 'Prénom'
            ],
            'label' => false,
            'constraints' => [
                new Assert\Length(['min' => 2, 'max' => 50]),
                new Assert\NotBlank()
            ]
        ])
        ->add('lastName', TextType::class, [
            'attr' => [
                'class' => 'form-control mt-2',
                'minlength' => '2',
                'maxlength' => '50',
                'placeholder' => 'Nom'
            ],
            'label' => false,
            'constraints' => [
                new Assert\Length(['min' => 2, 'max' => 50]),
                new Assert\NotBlank()
            ]
        ])
        ->add('email', TextType::class, [
            'attr' => [
                'class' => 'form-control mt-4',
                'minlength' => '2',
                'maxlength' => '50',
                'placeholder' => 'Adresse email'
            ],
            'label' => false,
            'constraints' => [
                new Assert\Length(['min' => 2, 'max' => 50]),
                new Assert\NotBlank()
            ]
        ])
        ->add('tel', TextType::class, [
            'attr' => [
                'class' => 'form-control mt-4',
                'minlength' => '2',
                'maxlength' => '20',
                'placeholder' => 'Téléphone'
            ],
            'label' => false,
            'constraints' => [
                new Assert\Length(['min' => 2, 'max' => 20]),
            ],
            'required' => false
        ])
        ->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-danger mt-4'
            ],
            'label' => 'Enregistrer les modifications'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commercial::class,
        ]);
    }
}
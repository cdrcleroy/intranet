<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'form-control mt-2 mb-4',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Prénom'
                ],
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'form-label mb-1 mt-3'
                ],                
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'class' => 'form-control mt-2 mb-4',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Nom'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mb-1 mt-3'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Email'
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
                'required' => false
            ])
            ->add('tel', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '20',
                    'placeholder' => 'Numéro de téléphone'
                ],
                'label' => 'Téléphone',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('mobile', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Mobile'
                ],
                'label' => 'Mobile',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
                'required' => false
            ])
            ->add('fonction', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Fonction'
                ],
                'label' => 'Fonction',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                ],
                'required' => false
            ])
            ->add('entreprise', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Entreprise',
                    'disabled' => 'disabled'
                ],
                'label' => 'Entreprise',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('site', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '50',
                    'placeholder' => 'Site',
                    'disabled' => 'disabled'
                ],
                'label' => 'Site',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-danger mb-4'
                ],
                'label' => 'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'entrepriseId' => null,
        ]);
    }
}
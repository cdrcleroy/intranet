<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Ticket;
use App\Entity\Contact;
use App\Entity\Entreprise;
use App\Entity\TicketObjet;
use App\Entity\TicketStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ticket = $options['data'] ?? null;

        $builder
            ->add('object', EntityType::class, [
                'class' => TicketObjet::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control mt-2 mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => 'Objet'
                ],
                'label' => 'Objet',
                'label_attr' => [
                    'class' => 'form-label mb-3'
                ],
            ])
            ->add('subject', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => 'Sujet'
                ],
                'label' => 'Sujet',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control mb-4',
                    'placeholder' => 'Ouvert le',
                    'readonly' => true,
                ],
                'label' => 'Ouvert le',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'disabled' => true,
                'data' => $ticket->getCreatedAt()
            ])
            ->add('updatedAt', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '255',
                    'placeholder' => 'Modifié le',
                    'readonly' => true,
                ],
                'label' => 'Modifié le',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 255]),
                ],
                'data' => $ticket && $ticket->getUpdatedAt() !== null ? $ticket->getUpdatedAt()->format('d-m-Y') : null,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '255',
                    'placeholder' => 'Description',
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 255]),
                ]
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => $ticket->getSite(),
                    'readonly' => true,
                ],
                'label' => 'Site',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150]),
                    new Assert\NotBlank()
                ],
                'disabled' => true,
                'empty_data' => function (FormInterface $form) {
                    return $form->getData()->getName();
                },
            ])
            ->add('entreprise', EntityType::class, [
                'class' => Entreprise::class,
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => $ticket->getEntreprise(),
                    'readonly' => true,
                ],
                'label' => 'Entreprise',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150]),
                    new Assert\NotBlank()
                ],
                'disabled' => true,
                'empty_data' => function (FormInterface $form) {
                    return $form->getData()->getName();
                },
            ])
            ->add('contact', EntityType::class, [
                'class' => Contact::class,
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => $ticket->getContact(),
                    'readonly' => true,
                ],
                'label' => 'Emetteur',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150]),
                    new Assert\NotBlank()
                ],
                'disabled' => true,
                'empty_data' => function (FormInterface $form) {
                    return $form->getData()->getLastName();
                },
            ])
            ->add('status', EntityType::class, [
                'class' => TicketStatus::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control mb-4',
                    'placeholder' => 'Statut'
                ],
                'label' => 'Statut',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
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
            'data_class' => Ticket::class,
        ]);
    }
}
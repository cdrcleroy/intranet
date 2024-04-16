<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Repository\TicketStatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketType extends AbstractType
{
    private $repository;

    public function __construct(TicketStatusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ticket = $options['data'] ?? null;

        $statuses = $this->repository->findAll();
        $statusChoices = [];

        foreach ($statuses as $status) {
            $statusChoices[$status->getName()] = $status->getName();
        }

        $builder
            ->add('object', TextType::class, [
                'attr' => [
                    'class' => 'form-control mt-2 mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => 'Sujet'
                ],
                'label' => 'Objet',
                'label_attr' => [
                    'class' => 'form-label mb-3'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150]),
                    new Assert\NotBlank()
                ]
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
            ->add('createdAt', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '255',
                    'placeholder' => 'Ouvert le'
                ],
                'label' => 'Ouvert le',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 255]),
                ],
                'data' => $ticket->getCreatedAt()->format('d-m-Y')
            ])
            ->add('updatedAt', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '255',
                    'placeholder' => 'Modifié le'
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
                    'placeholder' => 'Description'
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 255]),
                ]
            ])
            ->add('site', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => $ticket->getSite(),
                ],
                'label' => 'Site',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('entreprise', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => $ticket->getEntreprise(),
                ],
                'label' => 'Entreprise',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('contact', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                    'minlength' => '2',
                    'maxlength' => '150',
                    'placeholder' => $ticket->getContact(),
                ],
                'label' => 'Emetteur',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 150]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('status', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control mb-4',
                ],
                'label' => 'Statut',
                'label_attr' => [
                    'class' => 'form-label mt-2'
                ],
                'choices' => $statusChoices,
                'required' => true,
                'placeholder' => $ticket->getStatus(),
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
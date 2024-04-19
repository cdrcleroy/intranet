<?php

namespace App\Controller;

use App\Entity\Ticket;
use DateTimeImmutable;
use App\Entity\Contact;
use App\Form\TicketType;
use App\Entity\Commercial;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TicketStatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TicketController extends AbstractController
{

    #[Route('/ticket/{id}/edition', 'ticket.edit', methods: ['GET', 'POST'])]
    public function edit(
        EntityManagerInterface $manager,
        Request $request,
        TicketRepository $repository, 
        int $id,
        ): Response
    {
        $ticket = $repository->find($id);
        
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        if ($user instanceof Contact && $user->getEntreprise() !== $ticket->getEntreprise()) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à modifier cette entreprise.");
        }

        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();

            $object = $form->get('object')->getData();
            $status = $form->get('status')->getData();

            $ticket->setObject($object);
            $ticket->setStatus($status);

            $ticket->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($ticket);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations du ticket ont bien été modifiées.'
            );

            return $this->redirectToRoute('ticket.tickets');
        }

        return $this->render('pages/ticket/edit.html.twig', [
            'ticket' => $ticket,
            'user' => $user,
            'form' => $form,
            'profil' => $role,
        ]);
    }

    #[Route('/ticket/nouveau', 'ticket.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        TicketStatusRepository $repository,
        ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        $status = $repository->findOneBy(['name' => 'Ouvert']);

        if ($user instanceof Contact) {
            $entreprise = $user->getEntreprise();
            $site = $user->getSite();
        }

        $ticket = new Ticket();

        $ticket->setEntreprise($entreprise)
            ->setSite($site)
            ->setContact($user)
            ->setStatus($status);
        
        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();

            $manager->persist($ticket);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'Le ticket a bien été créé.'
            );
            
            return $this->redirectToRoute('ticket.tickets');
        }

        return $this->render('pages/ticket/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'profil' => $role,
        ]);
    }

    #[Route('/tickets', 'ticket.tickets', methods: ['GET'])]
    public function tickets(
    ): Response
    {
        $user = $this->getUser();

        if ($user instanceof Contact) {
            $entreprise = $user->getEntreprise();
            $tickets = $entreprise->getTickets();
        } elseif ($user instanceof Commercial) {
            $tickets = $user->getTickets();
        }

        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        return $this->render('pages/ticket/tickets.html.twig', [
            'tickets' => $tickets,
            'profil' => $role,
            'user' => $user,
        ]);
    }

    #[Route('/ticket/{id}/supprimer', 'ticket.delete', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function delete(
        TicketRepository $repository,
        EntityManagerInterface $manager,
        int $id,
        ): Response
    {
        $ticket = $repository->findOneBy(['id' => $id]);

        $manager->remove($ticket);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le ticket a bien été supprimé.'
        );

        return $this->redirectToRoute('ticket.tickets');
    }

    #[Route('/tickets/enattente', 'ticket.pendingTickets', methods: ['GET'])]
    public function pendingTickets(
        TicketRepository $repository,
    ): Response
    {
        $tickets = $repository->findOpenTickets();

        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        return $this->render('pages/ticket/tickets.html.twig', [
            'tickets' => $tickets,
            'profil' => $role,
            'user' => $user,
        ]);
    }
}
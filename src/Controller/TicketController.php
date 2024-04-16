<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TicketController extends AbstractController
{

    #[Route('/ticket/{id}', 'ticket.edit', methods: ['GET', 'POST'])]
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
            $manager->persist($ticket);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations du profil ont bien été modifiées.'
            );

            return $this->redirectToRoute('ticket.edit', [
                'id' => $ticket->getId()
            ]);
        }

        return $this->render('pages/ticket/edit.html.twig', [
            'ticket' => $ticket,
            'user' => $user,
            'form' => $form,
            'profil' => $role,
        ]);
    }

    
}
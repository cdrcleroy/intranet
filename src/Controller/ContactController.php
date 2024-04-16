<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Entity\Commercial;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ContactController extends AbstractController
{
    
    #[Route('/profil/{id}', 'contact.profil', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function profil(
        Contact $contact, 
        Request $request,
        EntityManagerInterface $manager
        ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute('security.login');
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $manager->persist($contact);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations du profil ont bien été modifiées.'
            );

            return $this->redirectToRoute('contact.home');
        }
        
        return $this->render('pages/contact/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'profil' => $role,
            'contact' => $contact,
        ]);
    }

    #[Route('/contact/nouveau', 'contact.new', methods: ['GET', 'POST'])]
    public function new(
        Contact $contact,
        Request $request,
        EntityManagerInterface $manager
        ): Response
    {
        $entreprise = $contact->getEntreprise();

        $newContact = new Contact();

        $newContact->setEntreprise($entreprise);
        
        $form = $this->createForm(ContactType::class, $newContact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $newContact = $form->getData();

            $manager->persist($newContact);
            $manager->flush();
            
            $this->addFlash(
                'success',
                'Le contact a bien été créé.'
            );
            
            return $this->redirectToRoute('contact.contacts', ['id' => $contact->getId()]);
        }

        return $this->render('pages/contact/new.html.twig', [
            'form' => $form->createView(),
            'user' => $contact
        ]);
    }

    #[Route('/contacts', 'contact.contacts', methods: ['GET'])]
    public function contacts(
        ContactRepository $repository
        ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();
        $entreprise = null;

        if ($user instanceof Contact) {
            $entreprise = $user->getEntreprise();
            $contacts = $entreprise->getContacts();
        } elseif ($user instanceof Commercial) {
            $contacts = $repository->findAll();
        }

        return $this->render('pages/contact/contacts.html.twig', [
            'entreprise' => $entreprise,
            'contacts' => $contacts,
            'profil' => $role,
            'user' => $user,
        ]);
    }

    #[Route('/contact/{id}/supprimer', 'contact.delete', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function delete(
        ContactRepository $repository,
        EntityManagerInterface $manager,
        int $id,
        ): Response
    {
        $contact = $repository->findOneBy(['id' => $id]);

        $manager->remove($contact);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le contact a bien été supprimée.'
        );

        return $this->redirectToRoute('contact.contacts');
    }

    #[Route('/contact/{id}/edition', 'contact.edit', methods: ['GET', 'POST'])]
    public function edit(
        EntityManagerInterface $manager,
        Request $request,
        ContactRepository $repository,
        int $id,
    ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        $contact = $repository->findOneBy(['id' => $id]);

        if ($user instanceof Contact && $contact->getEntreprise() !== $user->getEntreprise()) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à modifier cette entreprise.");
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $manager->persist($contact);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations du profil ont bien été modifiées.'
            );

            return $this->redirectToRoute('contact.contacts');
        }
        
        return $this->render('pages/contact/edit.html.twig', [
            'user' => $user,
            'profil' => $role,
            'form' => $form,
            'contact' => $contact,
        ]);
    }
}
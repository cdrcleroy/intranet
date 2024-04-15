<?php

namespace App\Controller;

use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise/{id}', 'entreprise.edit', methods: ['GET', 'POST'])]
    public function edit(
        EntityManagerInterface $manager,
        Request $request,
        EntrepriseRepository $repository, 
        int $id,
        ): Response
    {
        $entreprise = $repository->find($id);
        
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        $form = $this->createForm(EntrepriseType::class, $entreprise);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entreprise = $form->getData();
            $manager->persist($entreprise);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations du profil ont bien été modifiées.'
            );

            return $this->redirectToRoute('entreprise.edit', [
                'id' => $entreprise->getId()
            ]);
        }

        return $this->render('pages/entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'user' => $user,
            'form' => $form,
            'profil' => $role,
        ]);
    }

    #[Route('/entreprises', 'entreprise.entreprises', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function entreprises(
        EntrepriseRepository $repository,
        ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        $entreprises = $repository->findAll();

        return $this->render('pages/entreprise/entreprises.html.twig', [
            'entreprises' => $entreprises,
            'profil' => $role,
            'user' => $user,
        ]);
    }

    #[Route('/entreprise/{id}/supprimer', 'entreprise.delete', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function delete(
        EntrepriseRepository $repository,
        EntityManagerInterface $manager,
        int $id,
        ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $manager->remove($entreprise);
        $manager->flush();

        $this->addFlash(
            'success',
            'L\'entreprise a bien été supprimée.'
        );

        return $this->redirectToRoute('entreprise.entreprises');
    }

    #[Route('/entreprise/{id}/sites', 'entreprise.sites', methods: ['GET'])]
    public function sites(
        EntrepriseRepository $repository,
        int $id,
    ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        $entreprise = $repository->findOneBy(['id' => $id]);

        $sites = $entreprise->getSites();

        return $this->render('pages/site/sites.html.twig', [
            'entreprise' => $entreprise,
            'sites' => $sites,
            'profil' => $role,
            'user' => $user,
        ]);
    }

    #[Route('/entreprise/{id}/contacts', 'entreprise.contacts', methods: ['GET'])]
    public function contacts(
        EntrepriseRepository $repository,
        int $id,
    ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        $entreprise = $repository->findOneBy(['id' => $id]);

        $contacts = $entreprise->getContacts();

        return $this->render('pages/contact/contacts.html.twig', [
            'entreprise' => $entreprise,
            'contacts' => $contacts,
            'profil' => $role,
            'user' => $user,
        ]);
    }
}
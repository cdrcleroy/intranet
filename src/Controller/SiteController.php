<?php

namespace App\Controller;

use App\Form\SiteType;
use App\Entity\Contact;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SiteController extends AbstractController
{
    #[Route('/site/{id}', 'site.edit', methods: ['GET', 'POST'])]
    public function edit(
        EntityManagerInterface $manager,
        Request $request,
        SiteRepository $repository, 
        int $id,
        ): Response
    {
        $site = $repository->find($id);

        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();
        
        if ($user instanceof Contact && $user->getSite() !== $site) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à modifier cette entreprise.");
        }

        $form = $this->createForm(SiteType::class, $site);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $site = $form->getData();
            $manager->persist($site);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations du profil ont bien été modifiées.'
            );

            return $this->redirectToRoute('site.edit', [
                'id' => $site->getId()
            ]);
        }

        return $this->render('pages/site/edit.html.twig', [
            'site' => $site,
            'user' => $user,
            'form' => $form,
            'profil' => $role,
        ]);
    }

    #[Route('/sites', 'site.sites', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function sites(
        SiteRepository $repository,
    ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        $sites = $repository->findAll();
        
        return $this->render('pages/site/sites.html.twig', [
            'sites' => $sites,
            'profil' => $role,
            'user' => $user,
        ]);
    }

    #[Route('/site/{id}/supprimer', 'site.delete', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function delete(
        SiteRepository $repository,
        EntityManagerInterface $manager,
        int $id,
        ): Response
    {
        $site = $repository->findOneBy(['id' => $id]);

        $manager->remove($site);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le site a bien été supprimé.'
        );

        return $this->redirectToRoute('site.sites');
    }

    #[Route('/site/{id}/contacts', 'site.contacts', methods: ['GET'])]
    public function contacts(
        SiteRepository $repository,
        int $id,
    ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        $site = $repository->findOneBy(['id' => $id]);

        $contacts = $site->getContact();

        return $this->render('pages/contact/contacts.html.twig', [
            'site' => $site,
            'contacts' => $contacts,
            'profil' => $role,
            'user' => $user,
        ]);
    }
}
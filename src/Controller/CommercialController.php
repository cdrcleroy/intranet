<?php

namespace App\Controller;

use App\Entity\Commercial;
use App\Form\CommercialType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommercialController extends AbstractController
{

    #[Route('/edition/{id}', 'commercial.edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Commercial $commercial, 
        Request $request,
        EntityManagerInterface $manager
        ): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        $form = $this->createForm(CommercialType::class, $commercial);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $commercial = $form->getData();
            $manager->persist($commercial);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les informations du profil ont bien été modifiées.'
            );

            return $this->redirectToRoute('commercial.home');
        }
        
        return $this->render('pages/commercial/edit.html.twig', [
            'user' => $commercial,
            'form' => $form
        ]);
    }
}
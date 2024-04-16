<?php

namespace App\Controller;

use App\Repository\EntrepriseRepository;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FactureController extends AbstractController
{
    #[Route('/entreprise/{id}/factures', 'facture.factures', methods: ['GET'])]
    public function factures(
        EntrepriseRepository $repository,
        int $id,
        ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();
        $entreprise = $repository->findOneBy(['id' => $id]);

        $files = $entreprise->getFactures();

        return $this->render('pages/facture/factures.html.twig', [
            'files' => $files,
            'profil' => $role,
            'user' => $user,
            'entreprise' => $entreprise,
        ]);
    }

    #[Route('/factures/{id}', 'facture.download', methods: ['GET'])]
    public function download(
        FactureRepository $factureRepository,
        int $id,
        ): Response
    {
        $facture = $factureRepository->findOneBy(['id' => $id]);

        dd($facture);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $facture->getName() . '"');
        $response->headers->set('Content-Length', filesize($facture->getName()));
        $response->setContent(file_get_contents($facture->getName()));

        return $response;
    }
}
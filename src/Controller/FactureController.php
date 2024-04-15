<?php

namespace App\Controller;

use App\Repository\EntrepriseRepository;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FactureController extends AbstractController
{
    #[Route('/entreprise/{id}/factures', 'facture.index', methods: ['GET'])]
    public function factures(
        EntrepriseRepository $repository,
        int $id,
        FileService $fileService,
        ): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();
        $entreprise = $repository->findOneBy(['id' => $id]);

        $files = $fileService->getFilesByCompany($entreprise->getSlug());

        return $this->render('pages/facture/index.html.twig', [
            'files' => $files,
            'profil' => $role,
            'user' => $user,
        ]);
    }

    #[Route('/entreprise/{id}/factures/{filename}', 'facture.download', methods: ['GET'])]
    public function download(
        EntrepriseRepository $repository,
        int $id,
        FileService $fileService,
        string $filename
        ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $filePath = $fileService->getFilePath($entreprise->getSlug(), $filename);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Content-Length', filesize($filePath));
        $response->setContent(file_get_contents($filePath));

        return $response;
    }
}
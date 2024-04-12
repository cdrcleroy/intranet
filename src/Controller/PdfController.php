<?php

namespace App\Controller;

use App\Repository\EntrepriseRepository;
use App\Service\PdfExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PdfController extends AbstractController
{
    #[Route('/entreprises/pdf', 'pdf.entreprises', methods: ['GET'])]
    public function entreprises(
        EntrepriseRepository $repository,
        PdfExporter $pdfExporter,
    ): Response
    {
        $entreprises = $repository->findAll();

        $data = [];
        foreach ($entreprises as $entreprise) {
            $data[] = [
                'Nom' => $entreprise->getName(), 
                'Adresse' => $entreprise->getAddress() ?? 'null',
                'Adresse 2' => $entreprise->getAddress2() ?? 'null', 
                'Code Postal' => $entreprise->getPostalCode() ?? 'null', 
                'Ville' => $entreprise->getCity() ?? 'null', 
                'Pays' => $entreprise->getCountry() ?? 'null', 
                'Site Web' => $entreprise->getWebsite() ?? 'null',
                'Email' => $entreprise->getEmail() ?? 'null',
                'Téléphone' => $entreprise->getTel() ?? 'null',
                'Téléphone 2' => $entreprise->getTel2() ?? 'null',
                'Fax' => $entreprise->getFax() ?? 'null',
                'SIREN' => $entreprise->getSiren() ?? 'null',
            ];
        }

        $response = $pdfExporter->export($data, 'entreprises.pdf');

        return $response;
    }
}
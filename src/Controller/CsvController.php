<?php

namespace App\Controller;

use App\Service\CsvExporter;
use App\Repository\SiteRepository;
use App\Repository\ContactRepository;
use App\Repository\EntrepriseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CsvController extends AbstractController
{
    #[Route('/entreprises/csv', 'csv.entreprises', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function entreprises(
        EntrepriseRepository $repository,
        CsvExporter $csvExporter,
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
        
        $response = $csvExporter->export($data, 'entreprises.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="entreprises.csv"');

        return $response;
    }

    #[Route('/sites/csv', 'csv.sites', methods: ['GET'])]
    public function sites(
        SiteRepository $repository,
        CsvExporter $csvExporter,
        int $id,
        ): Response
    {
        $sites = $repository->findAll();
        
        $data = [];
        foreach ($sites as $site) {
            $data[] = [
                'Nom' => $site->getName(), 
                'Adresse' => $site->getAddress() ?? 'null',
                'Adresse 2' => $site->getAddress2() ?? 'null', 
                'Code Postal' => $site->getPostalCode() ?? 'null', 
                'Ville' => $site->getCity() ?? 'null', 
                'Pays' => $site->getCountry() ?? 'null', 
                'Email' => $site->getEmail() ?? 'null',
                'Téléphone' => $site->getTel() ?? 'null',
                'Téléphone 2' => $site->getTel2() ?? 'null',
                'Fax' => $site->getFax() ?? 'null',
                'Entreprise' => $site->getEntreprise() ?? 'null',
            ];
        }
        
        $response = $csvExporter->export($data, 'sites.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="sites.csv"');

        return $response;
    }

    #[Route('/entreprise/{id}/sites/csv', 'csv.sitesByEntreprise', methods: ['GET'])]
    public function sitesByEntreprise(
        EntrepriseRepository $repository,
        int $id,
        CsvExporter $csvExporter,
        ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $sites = $entreprise->getSites();

        $data = [];
        foreach ($sites as $site) {
            $data[] = [
                'Nom' => $site->getName(), 
                'Adresse' => $site->getAddress() ?? 'null',
                'Adresse 2' => $site->getAddress2() ?? 'null', 
                'Code Postal' => $site->getPostalCode() ?? 'null', 
                'Ville' => $site->getCity() ?? 'null', 
                'Pays' => $site->getCountry() ?? 'null', 
                'Email' => $site->getEmail() ?? 'null',
                'Téléphone' => $site->getTel() ?? 'null',
                'Téléphone 2' => $site->getTel2() ?? 'null',
                'Fax' => $site->getFax() ?? 'null',
                'Entreprise' => $site->getEntreprise() ?? 'null',
            ];
        }
        
        $response = $csvExporter->export($data, 'sites.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $entreprise->getSlug() . '_sites.csv"');

        return $response;
    }

    #[Route('/contacts/csv', 'csv.contacts', methods: ['GET'])]
    public function contacts(
        ContactRepository $repository,
        CsvExporter $csvExporter,
        ): Response
    {
        $contacts = $repository->findAll();

        $data = [];
        foreach ($contacts as $contact) {
            $data[] = [
                'Prénom' => $contact->getFirstname(), 
                'Nom' => $contact->getLastname() ?? 'null',
                'Téléphone' => $contact->getTel() ?? 'null', 
                'Mobile' => $contact->getMobile() ?? 'null', 
                'Email' => $contact->getEmail() ?? 'null', 
                'Fonction' => $contact->getFonction() ?? 'null', 
                'Entreprise' => $contact->getEntreprise() ?? 'null',
                'Site' => $contact->getSite() ?? 'null',
            ];
        }
        
        $response = $csvExporter->export($data, 'contacts.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="contacts.csv"');

        return $response;
    }

    #[Route('/entreprise/{id}/contacts/csv', 'csv.contactsByEntreprise', methods: ['GET'])]
    public function contactsByEntreprise(
        EntrepriseRepository $repository,
        int $id,
        CsvExporter $csvExporter,
        ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $contacts = $entreprise->getContacts();

        $data = [];
        foreach ($contacts as $contact) {
            $data[] = [
                'Prénom' => $contact->getFirstname(), 
                'Nom' => $contact->getLastname() ?? 'null',
                'Téléphone' => $contact->getTel() ?? 'null', 
                'Mobile' => $contact->getMobile() ?? 'null', 
                'Email' => $contact->getEmail() ?? 'null', 
                'Fonction' => $contact->getFonction() ?? 'null', 
                'Entreprise' => $contact->getEntreprise() ?? 'null',
                'Site' => $contact->getSite() ?? 'null',
            ];
        }
        
        $response = $csvExporter->export($data, 'contacts.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $entreprise->getSlug() . '_contacts.csv"');

        return $response;
    }

    #[Route('/site/{id}/contacts/csv', 'csv.contactsBySite', methods: ['GET'])]
    public function contactsBySite(
        SiteRepository $repository,
        int $id,
        CsvExporter $csvExporter,
        ): Response
    {
        $site = $repository->findOneBy(['id' => $id]);

        $contacts = $site->getContact();

        $data = [];
        foreach ($contacts as $contact) {
            $data[] = [
                'Prénom' => $contact->getFirstname(), 
                'Nom' => $contact->getLastname() ?? 'null',
                'Téléphone' => $contact->getTel() ?? 'null', 
                'Mobile' => $contact->getMobile() ?? 'null', 
                'Email' => $contact->getEmail() ?? 'null', 
                'Fonction' => $contact->getFonction() ?? 'null', 
                'Entreprise' => $contact->getEntreprise() ?? 'null',
                'Site' => $contact->getSite() ?? 'null',
            ];
        }
        
        $response = $csvExporter->export($data, 'contacts.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $site->getSlug() . '_contacts.csv"');

        return $response;
    }
}
<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Commercial;
use App\Service\CsvExporter;
use App\Repository\SiteRepository;
use App\Repository\TicketRepository;
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

    #[Route('/entreprise/{id}/csv', 'csv.entreprise', methods: ['GET'])]
    public function entreprise(
        EntrepriseRepository $repository,
        CsvExporter $csvExporter,
        int $id,
    ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

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

        $response = $csvExporter->export($data, $entreprise->getSlug() . '.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $entreprise->getSlug() . '.csv"');

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

    #[Route('/site/{id}/csv', 'csv.site', methods: ['GET'])]
    public function site(
        SiteRepository $repository,
        int $id,
        CsvExporter $csvExporter,
    ): Response
    {
        $site = $repository->findOneBy(['id' => $id]);

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

        $response = $csvExporter->export($data, $site->getSlug() . '.csv"');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $site->getSlug() . '.csv"');

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
        
        $response = $csvExporter->export($data, $entreprise->getSlug() . '_sites.csv"');
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
        
        $response = $csvExporter->export($data, $entreprise->getSlug() . '_contacts.csv"');
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
        
        $response = $csvExporter->export($data, $site->getSlug() . '_contacts.csv"');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $site->getSlug() . '_contacts.csv"');

        return $response;
    }

    #[Route('/contact/{id}/csv', 'csv.contact', methods: ['GET'])]
    public function contact(
        ContactRepository $repository,
        int $id,
        CsvExporter $csvExporter,
    ): Response
    {
        $contact = $repository->findOneBy(['id' => $id]);

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

        $response = $csvExporter->export($data, $contact->getLastName() . '_' . $contact->getFirstName() . '.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $contact->getLastName() . '_' . $contact->getFirstName() . '.csv"');

        return $response;
    }

    #[Route('/entreprise/{id}/factures/csv', 'csv.factures', methods: ['GET'])]
    public function factures(
        EntrepriseRepository $repository,
        int $id,
        CsvExporter $csvExporter,
        ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $files = $entreprise->getFactures();

        $data = [];
        foreach ($files as $file) {
            $data[] = [
                'Date' => $file->getCreatedAt()->format('Y-m-d H:i:s'), 
                'Nom' => $file->getName(),
            ];
        }

        $response = $csvExporter->export($data, $entreprise->getSlug() . '_factures.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $entreprise->getSlug() . '_factures.csv"');

        return $response;
    }

    #[Route('/ticket/{id}/csv', 'csv.ticket', methods: ['GET'])]
    public function ticket(
        TicketRepository $repository,
        int $id,
        CsvExporter $csvExporter,
    ): Response
    {
        $ticket = $repository->findOneBy(['id' => $id]);

        $data[] = [
            'Numéro' => $ticket->getId(), 
            'Date d\'ouverture' => $ticket->getCreatedAt()->format('Y-m-d H:i:s'),
            'Objet' => $ticket->getobject() ?? 'null', 
            'Sujet' => $ticket->getSubject() ?? 'null', 
            'Entreprise' => $ticket->getEntreprise() ?? 'null', 
            'Site' => $ticket->getSite() ?? 'null', 
            'Emetteur' => $ticket->getContact() ?? 'null',
        ];

        $response = $csvExporter->export($data, $ticket->getId() . '.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $ticket->getId() . '.csv"');

        return $response;
    }

    #[Route('/tickets/csv', 'csv.tickets', methods: ['GET'])]
    public function tickets(
        CsvExporter $csvExporter,
    ): Response
    {
        $user = $this->getUser();

        if ($user instanceof Contact) {
            $entreprise = $user->getEntreprise();
            $tickets = $entreprise->getTickets();
        } elseif ($user instanceof Commercial) {
            $tickets = $user->getTickets();
        }

        $data = [];

        foreach ($tickets as $ticket) {
            $data[] = [
                'Numéro' => $ticket->getId(), 
                'Date d\'ouverture' => $ticket->getCreatedAt()->format('Y-m-d H:i:s'),
                'Objet' => $ticket->getobject() ?? 'null', 
                'Sujet' => $ticket->getSubject() ?? 'null', 
                'Entreprise' => $ticket->getEntreprise() ?? 'null', 
                'Site' => $ticket->getSite() ?? 'null', 
                'Emetteur' => $ticket->getContact() ?? 'null',
            ];
        }

        $response = $csvExporter->export($data, 'tickets.csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="tickets.csv');

        return $response;
    }
}
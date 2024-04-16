<?php

namespace App\Controller;

use App\Service\PdfExporter;
use App\Repository\SiteRepository;
use App\Repository\ContactRepository;
use App\Repository\EntrepriseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PdfController extends AbstractController
{
    #[Route('/entreprises/pdf', 'pdf.entreprises', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function entreprises(
        EntrepriseRepository $repository,
        PdfExporter $pdfExporter,
    ): Response
    {
        $entreprises = $repository->findAll();

        $html = $this->renderView('pages/pdf/pdf_entreprises.html.twig', [
            'entreprises' => $entreprises,
        ]);

        $response = $pdfExporter->export('entreprises.pdf', $html);

        return $response;
    }

    #[Route('/entreprise/{id}/pdf', 'pdf.entreprise', methods: ['GET'])]
    public function entreprise(
        EntrepriseRepository $repository,
        PdfExporter $pdfExporter,
        int $id,
    ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $html = $this->renderView('pages/pdf/pdf_entreprise.html.twig', [
            'entreprise' => $entreprise,
        ]);

        $response = $pdfExporter->export($entreprise->getSlug() . ".pdf", $html);

        return $response;
    }

    #[Route('/sites/pdf', 'pdf.sites', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function sites(
        SiteRepository $repository,
        PdfExporter $pdfExporter,
    ): Response
    {
        $sites = $repository->findAll();

        $html = $this->renderView('pages/pdf/pdf_sites.html.twig', [
            'sites' => $sites,
        ]);

        $response = $pdfExporter->export('sites.pdf', $html);

        return $response;
    }

    #[Route('/site/{id}/pdf', 'pdf.site', methods: ['GET'])]
    public function site(
        SiteRepository $repository,
        PdfExporter $pdfExporter,
        int $id,
    ): Response
    {
        $site = $repository->findOneBy(['id' => $id]);

        $html = $this->renderView('pages/pdf/pdf_site.html.twig', [
            'site' => $site,
        ]);

        $response = $pdfExporter->export( $site->getSlug() . '.pdf', $html);

        return $response;
    }

    #[Route('/entreprise/{id}/sites/pdf', 'pdf.sitesByEntreprise', methods: ['GET'])]
    public function sitesByEntreprise(
        EntrepriseRepository $repository,
        int $id,
        PdfExporter $pdfExporter,
        ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $sites = $entreprise->getSites();

        $html = $this->renderView('pages/pdf/pdf_sites.html.twig', [
            'sites' => $sites,
        ]);

        $response = $pdfExporter->export('sites.pdf', $html);

        return $response;
    }

    #[Route('/contacts/pdf', 'pdf.contacts', methods: ['GET'])]
    #[IsGranted('ROLE_COMMERCIAL')]
    public function contacts(
        ContactRepository $repository,
        PdfExporter $pdfExporter,
    ): Response
    {
        $contacts = $repository->findAll();

        $html = $this->renderView('pages/pdf/pdf_contacts.html.twig', [
            'contacts' => $contacts,
        ]);

        $response = $pdfExporter->export('contacts.pdf', $html);

        return $response;
    }

    #[Route('/contact/{id}/pdf', 'pdf.contact', methods: ['GET'])]
    public function contact(
        ContactRepository $repository,
        int $id,
        PdfExporter $pdfExporter,
    ): Response
    {
        $contact = $repository->findOneBy(['id' => $id]);

        $html = $this->renderView('pages/pdf/pdf_contact.html.twig', [
            'contact' => $contact,
        ]);

        $response = $pdfExporter->export( $contact->getLastName() . '_' . $contact->getFirstName() . '.csv', $html);

        return $response;
    }

    #[Route('/entreprise/{id}/contacts/pdf', 'pdf.contactsByEntreprise', methods: ['GET'])]
    public function contactsByEntreprise(
        EntrepriseRepository $repository,
        int $id,
        PdfExporter $pdfExporter,
        ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $contacts = $entreprise->getContacts();

        $html = $this->renderView('pages/pdf/pdf_contacts.html.twig', [
            'contacts' => $contacts,
        ]);

        $response = $pdfExporter->export( $entreprise->getSlug(). '_contacts.pdf', $html);

        return $response;
    }

    #[Route('/site/{id}/contacts/pdf', 'pdf.contactsBySite', methods: ['GET'])]
    public function contactsBySite(
        SiteRepository $repository,
        int $id,
        PdfExporter $pdfExporter,
        ): Response
    {
        $site = $repository->findOneBy(['id' => $id]);

        $contacts = $site->getContact();

        $html = $this->renderView('pages/pdf/pdf_contacts.html.twig', [
            'contacts' => $contacts,
        ]);

        $response = $pdfExporter->export('contacts.pdf', $html);

        return $response;
    }

    #[Route('/entreprise/{id}/factures/pdf', 'pdf.factures', methods: ['GET'])]
    public function factures(
        EntrepriseRepository $repository,
        int $id,
        PdfExporter $pdfExporter,
        ): Response
    {
        $entreprise = $repository->findOneBy(['id' => $id]);

        $files = $entreprise->getFactures();

        $html = $this->renderView('pages/pdf/pdf_factures.html.twig', [
            'files' => $files,
        ]);

        $response = $pdfExporter->export('files.pdf', $html);

        return $response;
    }
}
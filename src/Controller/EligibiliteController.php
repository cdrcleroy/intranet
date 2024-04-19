<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EligibiliteController extends AbstractController
{
    #[Route('/eligibilite', 'eligibilite.index', methods: ['GET'])]
    public function index(): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        return $this->render('pages/eligibilite/index.html.twig', [
            'user' => $user,
            'profil' => $role,
        ]);
    }

    #[Route('/eligibilite/test', 'eligibilite.test', methods: ['GET', 'POST'])]
    public function test(Request $request): Response
    {
        $adresse = $request->request->get('adresse');
        
        $url = 'https://api-adresse.data.gouv.fr/search/?q=' . urlencode($adresse);
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $latitude = $data['features'][0]['geometry']['coordinates'][1];
        $longitude = $data['features'][0]['geometry']['coordinates'][0];

        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';
        $user = $this->getUser();

        return $this->render('pages/eligibilite/test.html.twig', [
            'adresse' => $adresse,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'user' => $user,
            'profil' => $role,
        ]);
    }
}
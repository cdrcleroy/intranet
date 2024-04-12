<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Commercial;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', 'home.index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $role = $this->isGranted('ROLE_COMMERCIAL') ? 'commercial' : 'contact';

        $user = $this->getUser();

        if ($user instanceof Commercial) {

            return $this->render('pages/commercial/home.html.twig', [
                'user' => $user,
                'profil' => $role,
            ]);
        } elseif ($user instanceOf Contact) {

            return $this->render('pages/contact/home.html.twig', [
                'user' => $user,
                'profil' => $role,
            ]);
        } else {
            return $this->redirectToRoute('security.login');
        }        
    }
}
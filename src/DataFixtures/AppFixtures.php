<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Site;
use Faker\Generator;
use App\Entity\Ticket;
use App\Entity\Contact;
use App\Entity\Commercial;
use App\Entity\Entreprise;
use App\Entity\TicketObjet;
use App\Entity\TicketStatus;
use Symfony\Component\Dotenv\Dotenv;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    private $slugger;

    public function __construct(
        SluggerInterface $slugger,
        )
    {
        $this->faker = Factory::create('fr_FR');
        $this->slugger = $slugger;
    }
    
    public function load(ObjectManager $manager): void
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../.env');

        $facturesDirectory = getenv('FACTURES_DIRECTORY');

        for ($i=0; $i < 30; $i++) { 
            $entreprise = new Entreprise($this->slugger);

            $name = $this->faker->company();
            $entreprise->setName($name)
                    ->setAddress($this->faker->streetAddress())
                    ->setAddress2(mt_rand(0, 1) == 1 ? $this->faker->secondaryAddress() : null)
                    ->setPostalCode($this->faker->postcode())
                    ->setCity($this->faker->city())
                    ->setCountry('France')
                    ->setWebsite($this->faker->domainName())
                    ->setTel($this->faker->phoneNumber())
                    ->setTel2(mt_rand(0, 1) == 1 ? $this->faker->phoneNumber() : null)
                    ->setFax(mt_rand(0, 1) == 1 ? $this->faker->phoneNumber() : null)
                    ->setSiren($this->faker->siren())
                    ->setSlug($name)
                    ->setEmail('test@mail.com');

            $entrepriseDirectory = $facturesDirectory . '/' . $entreprise->getSlug();
            if (!file_exists($entrepriseDirectory)) {
                mkdir($entrepriseDirectory, 0777, true);
            }

            for ($j=0; $j < mt_rand(1, 3); $j++) { 
                $site = new Site($this->slugger);
                $site->setEntreprise($entreprise)
                    ->setName($this->faker->company())
                    ->setAddress($this->faker->streetAddress())
                    ->setAddress2(mt_rand(0, 1) == 1 ? $this->faker->secondaryAddress() : null)
                    ->setPostalCode($this->faker->postcode())
                    ->setCity($this->faker->city())
                    ->setCountry('France')
                    ->setTel($this->faker->phoneNumber())
                    ->setTel2(mt_rand(0, 1) == 1 ? $this->faker->phoneNumber() : null)
                    ->setFax(mt_rand(0, 1) == 1 ? $this->faker->phoneNumber() : null)
                    ->setSlug($name)
                    ->setEmail('test@mail.com');
                
                $manager->persist($site);

                $entreprise->addSite($site);
            }

            $manager->persist($entreprise);
        }

        $manager->flush();

        $entreprises = $manager->getRepository(Entreprise::class)->findAll();

        foreach ($entreprises as $entreprise) {
            foreach ($entreprise->getSites() as $site) {
                for ($k=0; $k < 3; $k++) { 
                    $contact = new Contact();
                    $contact->setEntreprise($entreprise)
                        ->setFirstName($this->faker->firstName())
                        ->setLastName($this->faker->lastName())
                        ->setEmail($this->faker->email())
                        ->setRoles(['ROLE_USER'])
                        ->setTel(mt_rand(0, 1) == 1 ? $this->faker->phoneNumber() : null)
                        ->setMobile($this->faker->phoneNumber())
                        ->setFonction($this->faker->jobTitle())
                        ->setPlainPassword('password');
    
                    $site = $this->faker->randomElement($entreprise->getSites());
    
                    $site->addContact($contact);
    
                    $manager->persist($contact);
                }
            }
        }

        // Commerciaux
        for ($k=0; $k < 10; $k++) { 
            $commercial = new Commercial();

            $commercial->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName())
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_COMMERCIAL'])
                ->setTel($this->faker->phoneNumber())
                ->setPlainPassword('password');

            $manager->persist($commercial);

            $manager->flush();
        }

        $commerciaux = $manager->getRepository(Commercial::class)->findAll();

        $contacts = $manager->getRepository(Contact::class)->findAll();

        // TicketStatus
        $status1 = new TicketStatus();
        $status1->setName('Ouvert');
        $status1->setDescription('Le ticket est ouvert et en attente de traitement.');
        $status1->setColor('#FFC107');
        
        $manager->persist($status1);
        
        $status2 = new TicketStatus();
        $status2->setName('En cours');
        $status2->setDescription('Le ticket est ouvert et en cours de traitement.');
        $status2->setColor('#007BFF');
        
        $manager->persist($status2);
        
        $status3 = new TicketStatus();
        $status3->setName('En attente');
        $status3->setDescription('Le ticket est en attente.');
        $status3->setColor('#17A2B8');
        
        $manager->persist($status3);
        
        $status4 = new TicketStatus();
        $status4->setName('Résolu');
        $status4->setDescription('Le ticket est résolu.');
        $status4->setColor('#28A745');
        
        $manager->persist($status4);
        
        $status5 = new TicketStatus();
        $status5->setName('Fermé');
        $status5->setDescription('Le ticket est fermé.');
        $status5->setColor('#F8F9FA');
        
        $manager->persist($status5);
        
        $manager->flush();

        // TicketObject

        $object1 = new TicketObjet();
        $object1->setName('Question');

        $manager->persist($object1);

        $object2 = new TicketObjet();
        $object2->setName('Evolution de configuration');

        $manager->persist($object2);

        $object3 = new TicketObjet();
        $object3->setName('Dysfonctionnement');

        $manager->persist($object3);

        $object4 = new TicketObjet();
        $object4->setName('Intervention deniveau 1');

        $manager->persist($object4);

        $object5 = new TicketObjet();
        $object5->setName('Maintenance programmée par le client');

        $manager->persist($object5);

        $manager->flush();

        $allStatus = $manager->getRepository(TicketStatus::class)->findAll();
        $sites = $manager->getRepository(Site::class)->findAll();
        $objects = $manager->getRepository(TicketObjet::class)->findAll();
        
        // Tickets        
        foreach ($entreprises as $entreprise) {
            foreach ($contacts as $contact) {
                // Créer un ticket avec des données aléatoires
                $status = $this->faker->randomElement($allStatus);
                $site = $this->faker->randomElement($sites);
                $object = $this->faker->randomElement($objects);
                $commercial = ($status->getName() === 'Ouvert') ? null : $this->faker->randomElement($commerciaux);
    
                $ticket = new Ticket();
                $ticket->setStatus($status)
                    ->setObject($object)
                    ->setContact(mt_rand(0, 1) == 1 ? $contact : null)
                    ->setEntreprise($entreprise)
                    ->setCommercial($commercial)
                    ->setSubject($this->faker->catchPhrase())
                    ->setDescription($this->faker->realText())
                    ->setSite($site);
    
                $manager->persist($ticket);
            }
        }
        
        $manager->flush();
    }
}
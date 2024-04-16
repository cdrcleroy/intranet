<?php

namespace App\Command;

use App\Entity\Facture;
use Symfony\Component\Mime\Email;
use Symfony\Component\Dotenv\Dotenv;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'scanFactures',
    description: 'This command periodically scans the factures directory',
)]
class ScanFacturesCommand extends Command
{

    private $mailer;
    
    private $repository;

    private $manager;
    
    public function __construct(
        EntrepriseRepository $repository, 
        MailerInterface $mailer,
        EntityManagerInterface $manager,
        )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->mailer = $mailer;
        $this->manager = $manager;

    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__.'/../../.env.dev.local');

        $io = new SymfonyStyle($input, $output);

        $previousTimestamps = [];

        $currentTimestamps = [];

        $timestampFile = 'C:\laragon\www\portail\factures\timestamps.json';

        if (file_exists($timestampFile)) {
            $previousTimestamps = json_decode(file_get_contents($timestampFile), true);
        }

        $facturesDirectory = 'C:\laragon\www\intranet\factures';

        $entreprises = scandir($facturesDirectory);

        foreach ($entreprises as $entreprise) {
            if ($entreprise === '.' || $entreprise === '..') {
                continue;
            }

            $entreprisePath = $facturesDirectory . '/' . $entreprise;

            if (is_dir($entreprisePath)) {

                $files = scandir($entreprisePath);
                
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    
                    $filePath = $entreprisePath . '/' . $file;
                    
                    $currentTimestamp = filemtime($filePath);

                    if (!isset($previousTimestamps[$entreprise][$file]) || $currentTimestamp > $previousTimestamps[$entreprise][$file]) {
                        echo "Nouveau fichier détecté ou fichier modifié : $file dans le dossier de l'entreprise $entreprise\n";
                        $io->success(sprintf("Nouveau fichier détecté ou fichier modifié : $file dans le dossier de l'entreprise $entreprise\n"));

                        $facture = new Facture();
                        $facture->setName($file);
                        // $facture->setCreatedAt(new \DateTimeImmutable(date('Y-m-d H:i:s', $currentTimestamp)));

                        $maxFilesPerEnterprise = 3;

                        $entrepriseObject = $this->repository->findOneBy(['slug' => $entreprise]);

                        $factures = $this->manager->getRepository(Facture::class)->findBy(['entreprise' => $entrepriseObject]);

                        $totalFiles = count($factures);

                        if ($totalFiles > $maxFilesPerEnterprise) {
                            usort($factures, function($a, $b) {
                                return $a->getCreatedAt() <=> $b->getCreatedAt();
                            });

                            $filesToDelete = $totalFiles - $maxFilesPerEnterprise;

                            for ($i = 0; $i < $filesToDelete; $i++) {
                                $this->manager->remove($factures[$i]);
                            }

                            $this->manager->flush();
                        }



                        if ($entrepriseObject) {
                            $email = $entrepriseObject->getEmail();
                            $facture->setEntreprise($entrepriseObject);

                            $io->success(sprintf('E-mail envoyé à %s pour l\'entreprise %s.', $email, $entreprise));
                            
                            $message = (new Email())
                                ->from('hello@example.com')
                                ->to('you@example.com')
                                ->subject('Time for Symfony Mailer!')
                                ->text('Sending emails is fun again!')
                                ->html('<p>See Twig integration for better HTML integration!</p>');

                            $this->mailer->send($message);

                        } else {
                            $io->error(sprintf('Impossible de trouver l\'entreprise correspondant au slug %s.', $entreprise));
                        }

                        $this->manager->persist($facture);
                        $this->manager->flush();

                    }

                    $currentTimestamps[$entreprise][$file] = $currentTimestamp;
                    
                }
            }
        }

        file_put_contents($timestampFile, json_encode($currentTimestamps));

        $io->success('La tâche de scan des factures est terminée.');

        return Command::SUCCESS;
    }
}
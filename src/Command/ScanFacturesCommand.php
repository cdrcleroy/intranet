<?php

namespace App\Command;

use Symfony\Component\Mime\Email;
use Symfony\Component\Dotenv\Dotenv;
use App\Repository\EntrepriseRepository;
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

    public function __construct(
        EntrepriseRepository $repository, 
        MailerInterface $mailer
        )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->mailer = $mailer;
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
        $dotenv->loadEnv(__DIR__.'/../../.env');

        
        $io = new SymfonyStyle($input, $output);

        // Récupérer les horodatages des fichiers enregistrés précédemment (s'ils existent)
        $previousTimestamps = []; // Charger les horodatages à partir d'un fichier précédemment enregistré (par exemple, JSON)

        // Initialiser le tableau des horodatages actuels
        $currentTimestamps = [];

        // Chemin vers le fichier contenant les horodatages précédents
        $timestampFile = $_ENV['TIMESTAMP_FILE'];

        // Vérifier si le fichier des horodatages précédents existe
        if (file_exists($timestampFile)) {
            $previousTimestamps = json_decode(file_get_contents($timestampFile), true);
        }

        // Chemin vers le dossier des factures
        $facturesDirectory = 'C:\laragon\www\portail\factures';

        // Scanner le dossier des factures
        $entreprises = scandir($facturesDirectory);

        // Parcourir chaque fichier dans le dossier des factures
        foreach ($entreprises as $entreprise) {
            // Ignorer les fichiers spéciaux "." et ".."
            if ($entreprise === '.' || $entreprise === '..') {
                continue;
            }

            // Chemin complet du fichier
            $entreprisePath = $facturesDirectory . '/' . $entreprise;

            // Vérifier si le fichier est un répertoire (entreprise)
            if (is_dir($entreprisePath)) {

                // Scanner le dossier de l'entreprise
                $files = scandir($entreprisePath);
                
                // Parcourir chaque fichier de l'entreprise
                foreach ($files as $file) {
                    // Ignorer les fichiers spéciaux "." et ".."
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    
                    // Chemin complet du fichier de la facture
                    $filePath = $entreprisePath . '/' . $file;
                    
                    // Récupérer l'horodatage du fichier
                    $currentTimestamp = filemtime($filePath);

                    // Comparer l'horodatage actuel avec l'enregistrement précédent
                    if (!isset($previousTimestamps[$entreprise][$file]) || $currentTimestamp > $previousTimestamps[$entreprise][$file]) {
                        // Nouveau fichier détecté ou fichier modifié
                        echo "Nouveau fichier détecté ou fichier modifié : $file dans le dossier de l'entreprise $entreprise\n";

                        $entrepriseObject = $this->repository->findOneBy(['slug' => $entreprise]);

                        if ($entrepriseObject) {
                            // Récupérer l'e-mail de l'entreprise
                            $email = $entrepriseObject->getEmail();

                            if($email) {
                                // envoyer mail a l'entreprise
                                $io->success(sprintf('E-mail envoyé à %s pour l\'entreprise %s.', $email, $entreprise));
                                $message = (new Email())
                                    ->from('expediteur@exemple.com')
                                    ->to('test_email@localhost')
                                    ->subject('Facture')
                                    ->text('Votre facture est disponible.');

                                $this->mailer->send($message);
                            } else {
                                $io->success(sprintf('Impossible de trouver l\'e-mail pour l\'entreprise %s.', $entreprise));
                            }
                        } else {
                            $io->error(sprintf('Impossible de trouver l\'entreprise correspondant au slug %s.', $entreprise));
                        }

                    }

                    // Ajouter l'horodatage actuel au tableau
                    $currentTimestamps[$entreprise][$file] = $currentTimestamp;
                    
                }
            }
        }

        // Enregistrer les horodatages actuels dans le fichier
        file_put_contents($timestampFile, json_encode($currentTimestamps));

        $io->success('La tâche de scan des factures est terminée.');

        return Command::SUCCESS;
    }
}
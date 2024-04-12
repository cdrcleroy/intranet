<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FileService
{
    private $fileDirectory;

    public function __construct(string $fileDirectory)
    {
        $this->fileDirectory = $fileDirectory;
    }

    public function getFilesByCompany(string $companySlug): array
    {
        $companyDirectory = $this->fileDirectory . DIRECTORY_SEPARATOR . $companySlug;

        if (!is_dir($companyDirectory)) {
            return []; // Retourne un tableau vide si le dossier de l'entreprise n'existe pas
        }

        $finder = new Finder();
        $files = [];

        // Recherche de fichiers dans le sous-dossier de l'entreprise
        $finder->files()->in($companyDirectory)->depth(0);

        foreach ($finder as $file) {
            $files[] = [
                'name' => $file->getFilename(),
                'downloadLink' => '/download/' . $companySlug . '/' . $file->getFilename(), // Lien de téléchargement
            ];
        }

        return $files;
    }

    public function getFacturesDirectory(): string
    {
        return $this->fileDirectory;
    }

    public function getFilePath(string $slug, string $filename): string
    {
        $filePath = $this->fileDirectory . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($filePath)) {
            throw new FileNotFoundException(sprintf('Le fichier "%s" pour l\'entreprise "%s" n\'existe pas.', $filename, $slug));
        }

        return $filePath;
    }
}
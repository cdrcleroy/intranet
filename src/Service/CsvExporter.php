<?php

namespace App\Service;

use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    public function export(array $data, string $filename): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($data) {
            $csv = Writer::createFromFileObject(new \SplTempFileObject());
            $csv->setDelimiter(';');
            $csv->setOutputBOM(Writer::BOM_UTF8);
            
            $headers = array_keys($data[0]);
            $csv->insertOne($headers);
            
            foreach ($data as $row) {
                $csv->insertOne($row);
            }

            $csv->output();
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
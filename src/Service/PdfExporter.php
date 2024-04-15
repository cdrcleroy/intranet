<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;

class PdfExporter
{
    public function export(string $filename, string $html): Response
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();
        
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

}
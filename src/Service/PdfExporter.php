<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;

class PdfExporter
{
    public function export(array $data, string $filename): Response
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($this->generateHtml($data));
        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();
        
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    private function generateHtml(array $data): string
    {
        $html = '<table border="1">';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }
}
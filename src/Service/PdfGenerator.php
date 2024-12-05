<?php
// src/Service/PdfGenerator.php
namespace App\Service;

use TCPDF;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class PdfGenerator
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    //TODO: implement certificate generation by course
    public function generatePdf($data)
    {
        $pdf = new Tcpdf();

        $pdf->setPageOrientation('L'); // Set the page orientation to Landscape
        $pdf->AddPage();


        $html = $this->twig->render('certificate/pm-certificate-con.html.twig', ['data' => $data]);
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output($data['templatePath'], 'F');
    }
}

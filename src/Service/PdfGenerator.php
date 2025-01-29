<?php
// src/Service/PdfGenerator.php
namespace App\Service;

use TCPDF;
use Twig\Environment;
use DateTimeInterface;

class PdfGenerator
{
    private const CERTIFICATE_FONT = 'helvetica';
    
    public function __construct(
        private readonly Environment $twig,
        private readonly string $projectDir
    ) {
    }

    /**
     * Generates a course completion certificate PDF
     *
     * @param array{
     *     studentName: string,
     *     courseName: string,
     *     completionDate: DateTimeInterface,
     *     outputPath: string,
     *     templatePath?: string
     * } $data
     */
    public function generateCourseCertificate(array $data): void
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins to 0 to allow background to reach edges
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);
        
        // Add page with no margins
        $pdf->AddPage('L');
        
        // Set background image to cover entire page
        $backgroundPath = isset($data['templatePath']) && file_exists($data['templatePath'])
            ? $data['templatePath']
            : $this->projectDir . '/assets/images/certificate-background.png';
            
        $pdf->Image($backgroundPath, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
        
        // Set text color to white
        $pdf->SetTextColor(255, 255, 255);
        
        // Student Name (centered and larger)
        $pdf->SetFont(self::CERTIFICATE_FONT, 'B', 32);
        $pdf->SetY(90);
        $pdf->Cell(0, 0, $data['studentName'], 0, 1, 'C');
        
        // Date (adjusted position - moved up and more to the left)
        $pdf->SetFont(self::CERTIFICATE_FONT, '', 14);
        $pdf->SetY(165);
        $pdf->Cell(237, 0, $data['completionDate']->format('F d, Y'), 0, 0, 'R');
        
        $pdf->Output($data['outputPath'], 'F');
    }
}

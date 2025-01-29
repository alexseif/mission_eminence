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
     *     outputPath: string
     * } $data
     */
    public function generateCourseCertificate(array $data): void
    {
        $pdf = $this->initializePdf();
        $pdf->AddPage('L');
        
        // Set background image
        $backgroundPath = $this->projectDir . '/assets/images/certificate-background.png';
        $pdf->Image($backgroundPath, 0, 0, 297, 210); // A4 Landscape dimensions
        
        // Add logo
        $logoPath = $this->projectDir . '/assets/images/mission-logo.png';
        $pdf->Image($logoPath, 120, 20, 60);
        
        // Certificate Title
        $pdf->SetFont(self::CERTIFICATE_FONT, 'B', 48);
        $pdf->SetTextColor(51, 51, 51);
        $pdf->SetY(70);
        $pdf->Cell(0, 0, 'CERTIFICATE', 0, 1, 'C');
        
        // Presented To text
        $pdf->SetFont(self::CERTIFICATE_FONT, '', 16);
        $pdf->SetY(100);
        $pdf->Cell(0, 0, 'Presented To:', 0, 1, 'C');
        
        // Student Name
        $pdf->SetFont(self::CERTIFICATE_FONT, 'B', 24);
        $pdf->SetY(110);
        $pdf->Cell(0, 0, $data['studentName'], 0, 1, 'C');
        
        // Achievement text
        $pdf->SetFont(self::CERTIFICATE_FONT, '', 14);
        $pdf->SetY(130);
        $pdf->MultiCell(0, 10, 
            "Who has fulfilled all the core requirements to\n" .
            "begin their journey to eminence through trading\n" .
            "and investing in the financial markets.", 
            0, 'C');
        
        // Course Name
        $pdf->SetFont(self::CERTIFICATE_FONT, 'B', 16);
        $pdf->SetY(160);
        $pdf->Cell(0, 0, $data['courseName'], 0, 1, 'C');
        
        // Date and Organization
        $pdf->SetFont(self::CERTIFICATE_FONT, '', 12);
        $pdf->SetY(180);
        $pdf->SetX(40);
        $pdf->Cell(100, 0, 'Mission Eminence', 0, 0, 'L');
        $pdf->Cell(100, 0, $data['completionDate']->format('F d, Y'), 0, 0, 'R');
        
        // Add the challenge badge
        $badgePath = $this->projectDir . '/assets/images/10-day-challenge-badge.png';
        $pdf->Image($badgePath, 130, 140, 40);
        
        $pdf->Output($data['outputPath'], 'F');
    }
    
    /**
     * Initializes PDF with common settings
     */
    private function initializePdf(): TCPDF
    {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(20, 20, 20);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(true, 20);
        
        return $pdf;
    }
}

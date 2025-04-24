<?php
require __DIR__ . '/fpdf/fpdf.php';
session_start();

// ... [existing authentication and database code] ...

// Create PDF with proper font configuration
$pdf = new FPDF();
$pdf->AddPage();

// Add a Unicode-compatible font (instead of default helvetica)
$pdf->SetFont('Arial','B',16); // Use built-in Arial font

// Rest of your PDF content
$pdf->Cell(0,10,'Course Registration Summary',0,1,'C');
// ... [remaining PDF content] ...

$pdf->Output('D', 'course_registration.pdf');
exit;
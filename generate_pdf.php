<?php
require __DIR__ . '/fpdf/fpdf.php'; // Use absolute path
session_start();
// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Student') {
    die("Unauthorized access");
}

require 'db_connection.php';

// Get semester and student info
$semester = (int)$_GET['semester'];
$stmt = $conn->prepare("SELECT name, email, programme FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Get registered courses
$stmt = $conn->prepare("SELECT c.course_code, c.course_name, c.credits 
                       FROM registrations r
                       JOIN courses c ON r.course_id = c.id
                       WHERE r.user_id = ? AND r.semester = ?");
$stmt->bind_param("ii", $_SESSION['user_id'], $semester);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Course Registration Summary',0,1);
$pdf->Ln(10);

// Student Info
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Name: '.$student['name'],0,1);
$pdf->Cell(0,10,'Programme: '.$student['programme'],0,1);
$pdf->Cell(0,10,'Semester: '.$semester,0,1);
$pdf->Ln(15);

// Course List
$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,10,'Course Code',1);
$pdf->Cell(100,10,'Course Name',1);
$pdf->Cell(30,10,'Credits',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
foreach($courses as $course) {
    $pdf->Cell(60,10,$course['course_code'],1);
    $pdf->Cell(100,10,$course['course_name'],1);
    $pdf->Cell(30,10,$course['credits'],1,0,'C');
    $pdf->Ln();
}

$pdf->Output('D', 'course_registration.pdf'); // Force download
exit;
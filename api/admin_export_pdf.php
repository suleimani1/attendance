<?php
require_once '../config.php';
require_once '../lib/auth.php';
require_login();
require_role(['admin']);


require_once '../vendor/fpdf/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Attendance Report',0,1,'C');
$pdf->Ln(5);

// Table headers
$pdf->SetFont('Arial','B',10);
$pdf->Cell(15,8,'ID',1);
$pdf->Cell(40,8,'Student',1);
$pdf->Cell(40,8,'Reg. No',1);
$pdf->Cell(30,8,'Class',1);
$pdf->Cell(25,8,'Session',1);
$pdf->Cell(40,8,'Timestamp',1);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$q="SELECT a.attendance_id,u.name,u.registration_number,c.class_name,s.session_id,a.timestamp,a.status
    FROM attendance a
    JOIN users u ON u.user_id=a.student_id
    JOIN classes c ON c.class_id=a.class_id
    JOIN sessions s ON s.session_id=a.session_id
    ORDER BY a.timestamp DESC LIMIT 200";
$res=$conn->query($q);
while($row=$res->fetch_assoc()){
  $pdf->Cell(15,8,$row['attendance_id'],1);
  $pdf->Cell(40,8,substr($row['name'],0,20),1);
  $pdf->Cell(40,8,substr($row['registration_number'],0,20),1);
  $pdf->Cell(30,8,$row['class_name'],1);
  $pdf->Cell(25,8,$row['session_id'],1);
  $pdf->Cell(40,8,$row['timestamp'],1);
  $pdf->Ln();
}


$filename = "attendance_".date('Ymd_His').".pdf";
$pdf->Output('D',$filename); 

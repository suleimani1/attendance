<?php
require_once '../config.php'; 
require_once '../lib/auth.php'; 
require_login(); 
require_role(['admin']);

$filename = "attendance_".date('Ymd_His').".csv";
header('Content-Type: text/csv'); 
header("Content-Disposition: attachment; filename=\"$filename\"");

$out=fopen('php://output','w');
fputcsv($out,['ID','Student Name','reg. number','Class','Session','Timestamp','Status']);
$q="SELECT a.attendance_id,u.name,u.registration_number,c.class_name,s.session_id,a.timestamp,a.status
    FROM attendance a
    JOIN users u ON u.user_id=a.student_id
    JOIN classes c ON c.class_id=a.class_id
    JOIN sessions s ON s.session_id=a.session_id";
    
$res=$conn->query($q);
while($r=$res->fetch_row()) fputcsv($out,$r);
fclose($out);

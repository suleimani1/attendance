<?php
require_once '../config.php'; 
require_once '../lib/auth.php'; 
require_login(); 
require_role(['admin']);

$id=intval($_POST['attendance_id']??0); 
$status=$_POST['status']??'present';

$stmt=$conn->prepare("UPDATE attendance SET status=? WHERE attendance_id=?");

$stmt->bind_param('si',$status,$id); 
$stmt->execute(); 
echo json_encode(['ok'=>true]);

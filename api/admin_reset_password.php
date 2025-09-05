<?php
require_once '../config.php'; 
require_once '../lib/auth.php'; 
require_login(); 
require_role(['admin']);

$uid=intval($_POST['user_id']??0); 
$hash=password_hash($_POST['new_password']??'123456', PASSWORD_BCRYPT);

$stmt=$conn->prepare("UPDATE users SET password=? WHERE user_id=?"); 
$stmt->bind_param('si',$hash,$uid); 
$stmt->execute();

echo json_encode(['ok'=>true]);

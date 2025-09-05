<?php
require_once '../config.php'; 
require_once '../lib/auth.php'; 
require_login(); 
require_role(['admin']);

$uid=intval($_POST['user_id']??0);
$stmt=$conn->prepare("DELETE FROM users WHERE user_id=?"); 
$stmt->bind_param('i',$uid); 
$stmt->execute();

echo json_encode(['ok'=>true]);

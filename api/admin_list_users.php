<?php
require_once '../config.php';
require_once '../lib/auth.php';
require_login();
require_role(['admin']);

$res = $conn->query("
  SELECT user_id,
         registration_number,
         name,
         program,
         morning_course,
         evening_course,
         role
  FROM users
  ORDER BY user_id DESC
");

$out = [];
while($row = $res->fetch_assoc()){
  $out[] = $row;
}

header('Content-Type: application/json');
echo json_encode($out);

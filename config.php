<?php
ob_start();

ini_set('session.cookie_lifetime', 86400); // 24 hours
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
$DB_HOST='127.0.0.1';
$DB_USER='root';
$DB_PASS='';
$DB_NAME='db';
 //smart_attendance
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
$conn->set_charset('utf8mb4');

/*   function json_out($arr, $code=200){
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode($arr);
  exit;
} */

?>
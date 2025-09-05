<?php

require_once '../config.php';
require_once '../lib/auth.php';
require_login();
require_role(['admin']);

// Collect POST input
$reg_no   = trim($_POST['registration_number'] ?? '');
$name     = trim($_POST['name'] ?? '');
$program  = trim($_POST['program'] ?? '');
$morning  = trim($_POST['morning_course'] ?? '');
$evening  = trim($_POST['evening_course'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if (!$reg_no || !$name || !$password || !in_array($role, ['teacher','admin'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare(
   "INSERT INTO users (registration_number, name, program, morning_course, evening_course, password, role) 
    VALUES (?,?,?,?,?,?,?)"
);
$stmt->bind_param('sssssss', $reg_no, $name, $program, $morning, $evening, $hash, $role);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'user_id' => $stmt->insert_id,
        'name'    => $name,
        'role'    => $role
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: '.$stmt->error]);
}

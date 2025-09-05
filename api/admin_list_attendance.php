<?php
require_once '../config.php';
require_once '../lib/auth.php';
require_login();
require_role(['admin']);

// Filters
$course_name = $_GET['course_name'] ?? '';
$date_from   = $_GET['date_from'] ?? '';  
$date_to     = $_GET['date_to'] ?? '';   
$status      = $_GET['status'] ?? '';     

$sql = "SELECT 
            a.attendance_id,
            u.registration_number,
            u.name AS student_name,
            u.program,
            a.course_name,
            c.class_name,
            a.session_id,
            a.timestamp,
            a.status
        FROM attendance a
        JOIN users u ON a.student_id = u.user_id
        LEFT JOIN classes c ON a.class_id = c.class_id
        WHERE 1=1";

$params = [];
$types  = "";

if (!empty($course_name)) {
    $sql .= " AND a.course_name LIKE ?";
    $params[] = "%".$course_name."%";
    $types   .= "s";
}

if (!empty($date_from)) {
    $sql .= " AND DATE(a.timestamp) >= ?";
    $params[] = $date_from;
    $types   .= "s";
}
if (!empty($date_to)) {
    $sql .= " AND DATE(a.timestamp) <= ?";
    $params[] = $date_to;
    $types   .= "s";
}

if (!empty($status) && in_array(strtolower($status), ['present', 'absent'])) {
    $sql .= " AND a.status = ?";
    $params[] = strtolower($status);
    $types   .= "s";
}

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row;
}

header('Content-Type: application/json');
echo json_encode($out);

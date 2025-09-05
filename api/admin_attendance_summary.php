<?php
require_once '../config.php';
require_once '../lib/auth.php';
require_login();
require_role(['admin']);

$course_name = $_GET['course_name'] ?? null;

$sql = "SELECT COUNT(*) as total,
               SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
               SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent
        FROM attendance a
        JOIN classes c ON a.class_id = c.class_id
        WHERE c.course_name = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $course_name);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$total = $result['total'];
$present = $result['present'];
$absent = $result['absent'];
$percent = $total ? round(($present / $total) * 100, 1) : 0;

echo json_encode([
    'total' => $total,
    'present' => $present,
    'absent' => $absent,
    'percent' => $percent
]);

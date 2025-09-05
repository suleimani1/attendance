<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';
require_once '../lib/auth.php';

header('Content-Type: application/json; charset=utf-8');

if (!function_exists('json_out')) {
    function json_out($data, $status = 200) {
        global $JSON_OUT_ALREADY;
        $JSON_OUT_ALREADY = true;
        if (ob_get_level()) ob_end_clean();
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

register_shutdown_function(function () {
    $err = error_get_last();
    if ($err !== null) {
        $fatal_types = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
        $is_fatal = in_array($err['type'], $fatal_types, true);
        if ($is_fatal && empty($GLOBALS['JSON_OUT_ALREADY'])) {
            error_log("Shutdown fatal error (student_attendance.php): " . print_r($err, true));
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Internal server error (see logs)']);
            }
        } else {
            error_log("Shutdown (non-fatal or already output) (student_attendance.php): " . print_r($err, true));
        }
    }
});

try {
    // session_start();
    if (empty($_SESSION['user'])) {
        json_out(['error' => 'Unauthorized'], 401);
    }

    $student_id = $_SESSION['user']['user_id'] ?? null;
    if (!$student_id) json_out(['error' => 'Unauthorized'], 401);

    if (!$conn) {
        json_out(['error' => 'Database connection failed'], 500);
    }

    // Fetch student program and courses
    $stmt = $conn->prepare("SELECT program, morning_course, evening_course, registration_number, name FROM users WHERE user_id=?");
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $student_res = $stmt->get_result()->fetch_assoc();
    if (!$student_res) {
        json_out(['error' => 'Student not found'], 404);
    }

    $program = $student_res['program'] ?? 'N/A';
    $morning_course = $student_res['morning_course'] ?? 'N/A';
    $evening_course = $student_res['evening_course'] ?? 'N/A';
    $registration_number = $student_res['registration_number'] ?? '';
    $student_name = $student_res['name'] ?? '';

    // Fetch attendance records
    $q = "SELECT a.attendance_id, a.status, a.timestamp, a.session_id, a.class_id, a.course_name, a.program
          FROM attendance a
          WHERE a.student_id = ?
          ORDER BY a.timestamp DESC";

    $stmt2 = $conn->prepare($q);
    $stmt2->bind_param('i', $student_id);
    $stmt2->execute();
    $res = $stmt2->get_result();

    $records = [];
    $present = 0;
    $absent = 0;
    while ($row = $res->fetch_assoc()) {
        $row['student_name'] = $student_name;
        $row['registration_number'] = $registration_number;
        $row['program'] = $row['program'] ?: $program;
        $records[] = $row;

        if (isset($row['status']) && strtolower($row['status']) === 'present') $present++;
        else $absent++;
    }

    $total = $present + $absent;
    $percent = $total ? round(($present / $total) * 100, 1) : 0;

    json_out([
        'summary' => [
            'program' => $program,
            'morning_course' => $morning_course,
            'evening_course' => $evening_course,
            'present' => $present,
            'absent'  => $absent,
            'total'   => $total,
            'percent' => $percent
        ],
        'records' => $records
    ]);

} catch (Exception $e) {
    error_log("Exception in student_attendance.php: " . $e->getMessage());
    json_out(['error' => 'Internal server error'], 500);
}

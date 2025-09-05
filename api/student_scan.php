<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../lib/auth.php';
require_once __DIR__ . '/../lib/haversine.php'; 

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
            error_log("Shutdown fatal error (student_scan.php): " . print_r($err, true));
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Internal server error (see logs)']);
            }
        } else {
            error_log("Shutdown (non-fatal or already output) (student_scan.php): " . print_r($err, true));
        }
    }
});

try {
    // Debug session info
    error_log("Session status: " . session_status());
    error_log("Session ID: " . session_id());
    error_log("Session data: " . print_r($_SESSION, true));
    error_log("Cookies: " . print_r($_COOKIE, true));

    // session check
    if (empty($_SESSION['user']) && empty($_SESSION['user_id'])) {
        error_log("student_scan: missing session user data");
        json_out(['error' => 'Unauthorized - please login again'], 401);
    }

    // Only accept POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_out(['error' => 'Invalid method'], 405);
    }

    // Get student_id from session 
    $student_id = $_SESSION['user']['user_id'] ?? $_SESSION['user_id'] ?? null;
    
    if (!$student_id) {
        error_log("student_scan: no student_id found in session");
        json_out(['error' => 'Unauthorized - user ID not found'], 401);
    }

    // Get input data and extract token properly
    $raw_token = trim($_POST['token'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    
    // Extract token from URL if necessary
    $token = $raw_token;
    
    // If token contains a URL, extract just the token part
    if (strpos($raw_token, '?token=') !== false) {
        parse_str(parse_url($raw_token, PHP_URL_QUERY), $params);
        $token = $params['token'] ?? '';
    }
    
    // Also handle case where full URL is passed
    if (filter_var($raw_token, FILTER_VALIDATE_URL)) {
        $url_parts = parse_url($raw_token);
        parse_str($url_parts['query'] ?? '', $query_params);
        $token = $query_params['token'] ?? '';
    }
    
    // Debug logging for token extraction
    error_log("Raw token received: " . $raw_token);
    error_log("Extracted token: " . $token);
    error_log("Token length: " . strlen($token));

    if ($token === '' || $latitude === '' || $longitude === '') {
        json_out(['error' => 'Invalid input - missing required fields'], 400);
    }

    if (!$conn) {
        error_log("student_scan: no DB connection");
        json_out(['error' => 'Database connection failed'], 500);
    }

    // Look up the session based on the QR token
    $session_stmt = $conn->prepare("SELECT session_id, class_id FROM sessions WHERE qr_token = ? AND expires_at > NOW()");
    if (!$session_stmt) {
        error_log("session prepare failed: " . $conn->error);
        json_out(['error' => 'Database error'], 500);
    }

    $session_stmt->bind_param('s', $token);
    $session_stmt->execute();
    $session_result = $session_stmt->get_result();

    if ($session_result->num_rows === 0) {
        error_log("No session found for token: " . $token);
        $session_stmt->close();
        json_out(['error' => 'Invalid or expired QR token'], 400);
    }

    $session = $session_result->fetch_assoc();
    $session_id = $session['session_id'];
    $class_id = $session['class_id'];
    $session_stmt->close();

    // NOW check if attendance already exists for this session and student
    $check_stmt = $conn->prepare("SELECT attendance_id FROM attendance WHERE student_id = ? AND session_id = ?");
    $check_stmt->bind_param('ii', $student_id, $session_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        json_out(['error' => 'Attendance already marked for this session'], 409);
    }
    $check_stmt->close();

    // Get class location and allowed radius for distance validation
    $class_stmt = $conn->prepare("SELECT latitude, longitude, radius FROM classes WHERE class_id = ?");
    if (!$class_stmt) {
        error_log("class prepare failed: " . $conn->error);
        json_out(['error' => 'Database error'], 500);
    }

    $class_stmt->bind_param('i', $class_id);
    $class_stmt->execute();
    $class_result = $class_stmt->get_result();
    $class = $class_result->fetch_assoc();
    $class_stmt->close();

    if (!$class) {
        error_log("Class not found for class_id: " . $class_id);
        json_out(['error' => 'Class not found'], 400);
    }

    // Calculate distance using the Haversine formula 
    $distance = haversine_meters(
        floatval($latitude), 
        floatval($longitude), 
        floatval($class['latitude']), 
        floatval($class['longitude'])
    );

    $allowed_radius = floatval($class['radius']);

    error_log("Student location: " . $latitude . ", " . $longitude);
    error_log("Class location: " . $class['latitude'] . ", " . $class['longitude']);
    error_log("Calculated distance: " . round($distance, 2) . "m");
    error_log("Allowed radius: " . $allowed_radius . "m");

    if ($distance > $allowed_radius) {
        error_log("Student is too far from class location. Distance: " . round($distance, 2) . "m, Allowed: " . $allowed_radius . "m");
        json_out(['error' => 'You are too far from the class location. Distance: ' . round($distance, 2) . 'm, Allowed: ' . $allowed_radius . 'm'], 400);
    }

    // Get additional user info 
    $user_stmt = $conn->prepare("SELECT program FROM users WHERE user_id = ?");
    $user_stmt->bind_param('i', $student_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();
    $program = $user['program'] ?? '';
    $user_stmt->close();

    // Get course name from class info
    $course_name = "Unknown Course"; // Default
    $course_stmt = $conn->prepare("SELECT course_name FROM classes WHERE class_id = ?");
    if ($course_stmt) {
        $course_stmt->bind_param('i', $class_id);
        $course_stmt->execute();
        $course_result = $course_stmt->get_result();
        if ($course_result->num_rows > 0) {
            $course = $course_result->fetch_assoc();
            $course_name = $course['course_name'] ?? "Unknown Course";
        }
        $course_stmt->close();
    }

    // FINALLY: Insert attendance record with valid session_id
    $stmt = $conn->prepare("
        INSERT INTO attendance (student_id, token, latitude, longitude, status, timestamp, session_id, class_id, course_name, program)
        VALUES (?, ?, ?, ?, 'present', NOW(), ?, ?, ?, ?)
    ");

    if (!$stmt) {
        error_log("student_scan prepare failed: " . $conn->error);
        json_out(['error' => 'Database error'], 500);
    }

    $stmt->bind_param('isssiiss', $student_id, $token, $latitude, $longitude, $session_id, $class_id, $course_name, $program);

    if ($stmt->execute()) {
        error_log("Attendance marked successfully for student: " . $student_id . ", session: " . $session_id);
        json_out(['ok' => true, 'message' => 'Attendance marked successfully']);
    } else {
        error_log("student_scan execute failed: " . $stmt->error);
        json_out(['error' => 'Failed to mark attendance'], 500);
    }

} catch (Exception $e) {
    error_log("Exception in student_scan.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    json_out(['error' => 'Internal server error'], 500);
}
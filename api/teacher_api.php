<?php

use Exception;

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
ob_start(); 

require_once '../config.php';
require_once '../lib/auth.php';

ob_clean();

$response = ['success' => false, 'error' => 'Unknown error'];

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    error_log("Session contents: " . print_r($_SESSION, true));
    
    if (!isset($_SESSION['user'])) {
        throw new Exception('Not logged in. Please login again.');
    }
    
    // Check if user has teacher role
    if ($_SESSION['user']['role'] !== 'teacher') {
        throw new Exception('Access denied. Teacher role required.');
    }

    // Get teacher_id from session - use different approaches to find it
    $teacher_id = $_SESSION['user']['user_id'] ?? 
                 $_SESSION['user']['id'] ?? 
                 (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);
    
    // If still not found, try to get from database using email/registration number
    if (empty($teacher_id)) {
        $email = $_SESSION['user']['email'] ?? null;
        $reg_no = $_SESSION['user']['registration_number'] ?? null;
        
        if ($email) {
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND role = 'teacher'");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $teacher_id = $result['user_id'] ?? null;
        }
        
        if (empty($teacher_id) && $reg_no) {
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE registration_number = ? AND role = 'teacher'");
            $stmt->bind_param('s', $reg_no);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $teacher_id = $result['user_id'] ?? null;
        }
    }

    // Final check if teacher_id is found
    if (empty($teacher_id)) {
        throw new Exception('Teacher ID not found. Please login again. Session: ' . json_encode($_SESSION['user']));
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('No action specified');
    }

    // Clean any output again before processing
    ob_clean();

    switch ($action) {
        case 'create_class':
            $response = createClass($conn, $teacher_id);
            break;
            
        case 'create_session_by_course':
            $response = createSessionByCourse($conn, $teacher_id);
            break;

        case 'list_attendance_by_course':
            $response = listAttendanceByCourse($conn, $teacher_id);
            break;

        case 'mark_present_by_course':
            $response = markPresentByCourse($conn, $teacher_id);
            break;

        case 'list_classes':
            $response = listClasses($conn, $teacher_id);
            break;

        default:
            throw new Exception('Invalid action: ' . $action);
    }

} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
}

// Clean any final output and send JSON
ob_end_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;

function createClass($conn, $teacher_id) {
    $class_name = trim($_POST['class_name'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $latitude = $_POST['latitude'] ?? 0;
    $longitude = $_POST['longitude'] ?? 0;
    $radius = (int)($_POST['radius'] ?? 100); 
    $schedule = $_POST['schedule'] ?? '';

    // Check if teacher_id is received
    if (empty($teacher_id)) {
        return ['success' => false, 'error' => 'Teacher ID is missing. Please login again.'];
    }

    if (!$class_name || !$course_name || !$latitude || !$longitude || !$schedule) {
        return ['success' => false, 'error' => 'All fields are required'];
    }

    $stmt = $conn->prepare("INSERT INTO classes (class_name, course_name, teacher_id, latitude, longitude, radius, schedule) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // Check if prepare failed
    if (!$stmt) {
        return ['success' => false, 'error' => 'Database prepare failed: ' . $conn->error];
    }
    
    $stmt->bind_param('ssiddis', $class_name, $course_name, $teacher_id, $latitude, $longitude, $radius, $schedule);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Failed to create class: ' . $stmt->error];
    }

    return ['success' => true, 'message' => 'Class created successfully', 'class_id' => $stmt->insert_id];
}

/* ========== UNIQUE QR TOKEN GENERATOR ========== */
function generateUniqueQrToken($conn, $maxAttempts = 10) {
    $attempts = 0;
    
    while ($attempts < $maxAttempts) {
        // Generate a random token (16 characters like your existing format)
        $token = substr(bin2hex(random_bytes(16)), 0, 16);
        
        // Check if token already exists
        $check_stmt = $conn->prepare("SELECT session_id FROM sessions WHERE qr_token = ?");
        $check_stmt->bind_param('s', $token);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows === 0) {
            $check_stmt->close();
            return $token;
        }
        
        $check_stmt->close();
        $attempts++;
    }
    
    return null; // Failed to generate unique token after max attempts
}

/* ========== SESSION CREATION FUNCTION ========== */
function createSessionByCourse($conn, $teacher_id) {
    $course_name  = trim($_POST['course_name'] ?? '');
    $session_date = $_POST['session_date'] ?? '';
    $session_time = $_POST['session_time'] ?? null;
    $ttl_minutes  = (int)($_POST['ttl_minutes'] ?? 10);

    if (!$course_name || !$session_date) {
        return ['success' => false, 'error' => 'Missing required fields'];
    }

    // Find class by course_name for this teacher
    $stmt = $conn->prepare("SELECT class_id FROM classes WHERE course_name=? AND teacher_id=? LIMIT 1");
    $stmt->bind_param('si', $course_name, $teacher_id);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Database query failed: ' . $stmt->error];
    }
    
    $class = $stmt->get_result()->fetch_assoc();
    if (!$class) {
        return ['success' => false, 'error' => "Course not found. Create a class with course_name='$course_name' first."];
    }

    // Generate a UNIQUE QR token (32 characters)
    $qr_token = bin2hex(random_bytes(16)); // 32-character token
    
    // DEBUG: Log the generated token
    error_log("Generated QR token: " . $qr_token);
    error_log("Token length: " . strlen($qr_token));
    error_log("Class ID: " . $class['class_id']);

    // Handle session_time
    if ($session_time) {
        $stmt = $conn->prepare("
            INSERT INTO sessions (class_id, session_date, session_time, is_active, qr_token, expires_at)
            VALUES (?,?,?,1,?, DATE_ADD(NOW(), INTERVAL ? MINUTE))
        ");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return ['success' => false, 'error' => 'Database prepare failed: ' . $conn->error];
        }
        // FIXED: qr_token must be bound as a string ("s")
        $stmt->bind_param('isssi', $class['class_id'], $session_date, $session_time, $qr_token, $ttl_minutes);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO sessions (class_id, session_date, is_active, qr_token, expires_at)
            VALUES (?,?,1,?, DATE_ADD(NOW(), INTERVAL ? MINUTE))
        ");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return ['success' => false, 'error' => 'Database prepare failed: ' . $conn->error];
        }
        // FIXED: qr_token must be bound as a string ("s")
        $stmt->bind_param('issi', $class['class_id'], $session_date, $qr_token, $ttl_minutes);
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return ['success' => false, 'error' => 'Failed to create session: ' . $stmt->error];
    }

    // DEBUG: Verify the token was actually inserted
    $inserted_id = $stmt->insert_id;
    error_log("Session created with ID: " . $inserted_id);
    
    // Verify the token was stored correctly
    $check_stmt = $conn->prepare("SELECT qr_token FROM sessions WHERE session_id = ?");
    $check_stmt->bind_param('i', $inserted_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result()->fetch_assoc();
    error_log("Stored token in DB: " . ($result['qr_token'] ?? 'NULL'));
    
    return ['success' => true, 'data' => ['qr_token' => $qr_token, 'session_id' => $inserted_id]];
}

/* ========== ATTENDANCE LISTING FUNCTION ========== */
function listAttendanceByCourse($conn, $teacher_id) {
    $course_name = trim($_GET['course_name'] ?? '');
    if (!$course_name) {
        return ['success' => false, 'error' => 'Course name required'];
    }

    $sql = "SELECT a.attendance_id, u.registration_number, u.name AS student_name,
                   a.program, a.course_name, c.class_name, a.session_id, 
                   a.timestamp, a.status
            FROM attendance a
            JOIN classes c ON a.class_id = c.class_id
            JOIN users u ON a.student_id = u.user_id
            WHERE a.course_name=? AND c.teacher_id=?
            ORDER BY a.timestamp DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $course_name, $teacher_id);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Database query failed'];
    }
    
    $res = $stmt->get_result();
    return ['success' => true, 'data' => $res->fetch_all(MYSQLI_ASSOC)];
}

/* ========== MANUAL ATTENDANCE MARKING FUNCTION ========== */
function markPresentByCourse($conn, $teacher_id) {
    $reg_no = trim($_POST['registration_number'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    
    if (!$reg_no || !$course_name) {
        return ['success' => false, 'error' => 'Registration number and course required'];
    }

    // Find student
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE registration_number=? AND role='student' LIMIT 1");
    $stmt->bind_param('s', $reg_no);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Student lookup failed'];
    }
    
    $student = $stmt->get_result()->fetch_assoc();
    if (!$student) {
        return ['success' => false, 'error' => 'Student not found'];
    }

    // Get latest session
    $stmt = $conn->prepare(
        "SELECT se.session_id, se.class_id 
         FROM sessions se
         JOIN classes c ON se.class_id = c.class_id
         WHERE c.course_name=? AND c.teacher_id=?
         ORDER BY se.session_date DESC, se.session_id DESC
         LIMIT 1"
    );
    $stmt->bind_param('si', $course_name, $teacher_id);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Session lookup failed'];
    }
    
    $session = $stmt->get_result()->fetch_assoc();
    if (!$session) {
        return ['success' => false, 'error' => 'No sessions found for this course'];
    }

    // Mark attendance
    $stmt = $conn->prepare(
        "INSERT INTO attendance (session_id, student_id, class_id, course_name, timestamp, status)
         VALUES (?, ?, ?, ?, NOW(), 'present')
         ON DUPLICATE KEY UPDATE status='present', timestamp=NOW()"
    );
    $stmt->bind_param('iiis', $session['session_id'], $student['user_id'], $session['class_id'], $course_name);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Failed to mark attendance: ' . $stmt->error];
    }

    return ['success' => true, 'message' => 'Attendance marked successfully'];
}

/* ========== CLASS LISTING FUNCTION ========== */
function listClasses($conn, $teacher_id) {
    $stmt = $conn->prepare("SELECT class_id, class_name, course_name, latitude, longitude, radius, schedule 
                           FROM classes WHERE teacher_id=? ORDER BY class_id DESC");
    $stmt->bind_param('i', $teacher_id);
    
    if (!$stmt->execute()) {
        return ['success' => false, 'error' => 'Database query failed'];
    }
    
    $res = $stmt->get_result();
    return ['success' => true, 'data' => $res->fetch_all(MYSQLI_ASSOC)];
}
<?php
// session_start();
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$registration_number = trim($_POST['registration_number'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($registration_number) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Registration number and password are required']);
    exit;
}

try {
    
    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE registration_number = ?");
    $stmt->bind_param('s', $registration_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid registration number or password']);
        exit;
    }
    
    $user = $result->fetch_assoc();
   
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid registration number or password']);
        exit;
    }
    
    // Set session data 
    $_SESSION['user'] = [
        'user_id' => $user['user_id'],
        'name' => $user['name'],
        'role' => $user['role']
    ];
    
    $_SESSION['user_id'] = $user['user_id'];
    
    session_regenerate_id(true);
    
    // Return success with role for redirect
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'role' => $user['role'],
        'user_id' => $user['user_id']
    ]);
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>
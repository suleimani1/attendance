<?php
require_once '../config.php';

$reg_no   = trim($_POST['registration_number'] ?? '');
$name     = trim($_POST['name'] ?? '');
$program  = trim($_POST['program'] ?? '');
$morning  = trim($_POST['morning_course'] ?? '');
$evening  = trim($_POST['evening_course'] ?? '');
$pass     = $_POST['password'] ?? '';

if(!$reg_no || !$name || !$program || !$morning || !$evening || !$pass){
    http_response_code(400);
    echo json_encode(['error'=>'All fields are required']);
    exit;
}

$hash = password_hash($pass, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users(registration_number,name,program,morning_course,evening_course,password,role)
                        VALUES(?,?,?,?,?,?,'student')");
$stmt->bind_param("ssssss",$reg_no,$name,$program,$morning,$evening,$hash);

if($stmt->execute()){
    echo json_encode([
        'user_id'=>$stmt->insert_id,
        'registration_number'=>$reg_no,
        'name'=>$name,
        'program'=>$program,
        'morning_course'=>$morning,
        'evening_course'=>$evening,
        'role'=>'student'
    ]);
}else{
    http_response_code(400);
    echo json_encode(['error'=>$conn->error]);
}

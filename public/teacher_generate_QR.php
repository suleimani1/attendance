<?php

require_once 'config.php';
require_once 'lib/auth.php';
require_role('teacher');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $token = bin2hex(random_bytes(16));
    $class_id = $_POST['class_id'];
    $expires_at = date('Y-m-d H:i:s', time() + (60 * 10)); 


    $stmt = $conn->prepare("INSERT INTO sessions (class_id, qr_token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $class_id, $token, $expires_at);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate QR Code</title>

</head>
<body>
    <h2>Create a New QR Session</h2>

    <form method="POST">
        <label for="class_id">Class:</label>
        <select name="class_id" id="class_id" required>
            <option value="1">Mathematics 101</option>
            <option value="2">Computer Science</option>
        </select>
        <button type="submit">Generate QR Code</button>
    </form>

    <?php if (isset($token)): ?>
    <hr>
    
    <h3>Your QR Code</h3>
    <p>Token: <?php echo $token; ?></p>
    <p>Expires at: <?php echo $expires_at; ?></p>


    <img src="../api/generate_qr_image.php?token=<?php echo urlencode($token); ?>" alt="QR Code for Attendance">

    <p>URL: http://<?php echo $_SERVER['HTTP_HOST']; ?>/smart1-php/scan.php?token=<?php echo urlencode($token); ?></p>
    <?php endif; ?>
</body>
</html>
<?php
require_once '../config.php';
require_once '../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;

$token = $_GET['token'] ?? '';
$download = isset($_GET['download']);

if (empty($token)) {
    createErrorImage('Missing Token', $download);
    exit;
}

try {
    // Generate the URL for scanning
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $qr_content = $protocol . $host . "/smart1/public/student-scan.php?token=" . urlencode($token);
    
    // Create QR code
    $qrCode = QrCode::create($qr_content)
        ->setSize(400) 
        ->setMargin(20)
        ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh());
    
    
    $foregroundColor = new Color(0, 0, 0); 
    $backgroundColor = new Color(255, 255, 255); 
    $qrCode->setForegroundColor($foregroundColor);
    $qrCode->setBackgroundColor($backgroundColor);
    
    // Create writer
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    // Set headers
    header('Content-Type: ' . $result->getMimeType());
    
    if ($download) {
        header('Content-Disposition: attachment; filename="attendance_qr_' . $token . '.png"');
    } else {
        header('Cache-Control: max-age=3600');
    }
    
    // Output the QR code image
    echo $result->getString();
    
} catch (Exception $e) {
    error_log("QR generation error: " . $e->getMessage());
    createErrorImage('QR Generation Failed: ' . $e->getMessage(), $download);
}
exit;

// Function to create error images
function createErrorImage($message, $download = false) {
    header('Content-Type: image/png');
    if ($download) {
        header('Content-Disposition: attachment; filename="error.png"');
    }
    
    $width = 400;
    $height = 200;
    
    $im = imagecreate($width, $height);
    $bg = imagecolorallocate($im, 255, 255, 255); 
    $textColor = imagecolorallocate($im, 255, 0, 0); 
    
    // Add border
    $borderColor = imagecolorallocate($im, 200, 200, 200);
    imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
    
   
    $lines = [];
    $maxChars = 40;
    if (strlen($message) > $maxChars) {
        $words = explode(' ', $message);
        $currentLine = '';
        foreach ($words as $word) {
            if (strlen($currentLine . $word) <= $maxChars) {
                $currentLine .= $word . ' ';
            } else {
                $lines[] = trim($currentLine);
                $currentLine = $word . ' ';
            }
        }
        $lines[] = trim($currentLine);
    } else {
        $lines[] = $message;
    }
    
    // Add text lines
    $y = 70;
    foreach ($lines as $line) {
        imagestring($im, 3, 20, $y, $line, $textColor);
        $y += 20;
    }
    
    imagepng($im);
    imagedestroy($im);
}
?>
<?php

declare(strict_types=1);

@ini_set('display_errors', '0');          
@ini_set('zlib.output_compression', 'Off');

require_once __DIR__.'/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

$token    = $_GET['token'] ?? '';
$download = isset($_GET['download']);

if ($token === '') {
    http_response_code(400);
    exit('Token parameter is required');
}

$payload = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
         .'://'.$_SERVER['HTTP_HOST'].'/smart1/api/student_scan.php?token='
         .rawurlencode($token);

// Build QR 
$qr = QrCode::create($payload)
    ->setEncoding(new Encoding('UTF-8'))
    ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
    ->setSize(320)
    ->setMargin(10)
    ->setForegroundColor(new Color(0, 0, 0))
    ->setBackgroundColor(new Color(255, 255, 255));

$png = (new PngWriter())->write($qr);
$bytes = $png->getString();

while (ob_get_level() > 0) { ob_end_clean(); }

header('Content-Type: image/png');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=3600');
header('Content-Length: '.strlen($bytes));
if ($download) {
    header('Content-Disposition: attachment; filename="attendance_qr_'.$token.'.png"');
}
echo $bytes;
exit;

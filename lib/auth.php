<?php
function require_login() {
  if (empty($_SESSION['user'])) {
    header('Location: /smart1/public/index.php'); exit;
  }
}
function require_role($roles) {
  if (!in_array($_SESSION['user']['role'] ?? '', (array)$roles, true)) {
    http_response_code(403); echo "Forbidden"; 
    exit;
  }
}

?>
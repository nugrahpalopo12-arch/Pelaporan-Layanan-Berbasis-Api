<?php
if(session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/db.php';

function flash_set($key, $msg) {
  $_SESSION['flash'][$key] = $msg;
}
function flash_get($key) {
  if(!empty($_SESSION['flash'][$key])) {
    $m = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $m;
  }
  return null;
}

function e($s) {
  return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token() {
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['_csrf'];
}
function csrf_check($token) {
  return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token ?? '');
}

function log_action($user_id, $action, $meta = null){
  try {
    $pdo = DB::get();
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, meta) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $meta]);
  } catch (Exception $ex) {
    error_log("log_action error: ".$ex->getMessage());
  }
}

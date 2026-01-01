<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

if (!Auth::check() || Auth::user()['role'] !== 'admin') {
  die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!csrf_check($_POST['csrf'] ?? '')) {
    flash_set('error', 'Token keamanan tidak valid.');
    header("Location: index.php");
    exit;
  }

  $id = (int)($_POST['id'] ?? 0);
  $status = $_POST['status'] ?? '';

  $allowed = ['new', 'in_progress', 'resolved', 'rejected'];
  if (!in_array($status, $allowed, true)) {
    flash_set('error', 'Status tidak valid.');
    header("Location: index.php");
    exit;
  }

  $pdo = DB::get();
  $stmt = $pdo->prepare("UPDATE reports SET status = ?, updated_at = NOW() WHERE id = ?");
  $stmt->execute([$status, $id]);

  log_action(Auth::user()['id'], "update_report_status", "Report #$id -> $status");

  flash_set('success', 'Status laporan berhasil diperbarui.');
  header("Location: index.php");
  exit;
}

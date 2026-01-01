<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (!csrf_check($_POST['csrf_token'] ?? '')) {
    flash_set('error', "Token tidak valid, silakan coba lagi.");
    header("Location: ../public/register.php");
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($name === "" || $email === "" || $password === "") {
    flash_set('error', "Semua field wajib diisi.");
    header("Location: ../public/register.php");
    exit;
}

$pdo = DB::get();

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    flash_set('error', "Email sudah terdaftar!");
    header("Location: ../public/register.php");
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$role = "user";

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$ok = $stmt->execute([$name, $email, $hashedPassword, $role]);

if ($ok) {
    flash_set('success', "Pendaftaran berhasil! Silakan login.");
    header("Location: ../public/login.php");
    exit;
} else {
    flash_set('error', "Terjadi kesalahan, coba lagi.");
    header("Location: ../public/register.php");
    exit;
}

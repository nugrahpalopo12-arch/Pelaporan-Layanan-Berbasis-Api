<?php
session_start();
require_once __DIR__ . '/../../src/db.php';

header("Content-Type: application/json");

// Ambil data JSON
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validasi
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Email dan password wajib diisi"
    ]);
    exit;
}

// Query pakai PDO (BUKAN mysqli)
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    echo json_encode([
        "status" => true,
        "message" => "Login berhasil"
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        "status" => false,
        "message" => "Email atau password salah"
    ]);
}

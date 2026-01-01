<?php
require_once __DIR__ . '/../../src/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'user';

if (!$name || !$email || !$password) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

$pdo = DB::get();

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (name, email, password, role)
    VALUES (?, ?, ?, ?)
");

try {
    $stmt->execute([$name, $email, $hash, $role]);
    echo json_encode([
        "status" => true,
        "message" => "Registrasi berhasil"
    ]);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Email sudah terdaftar"
    ]);
}

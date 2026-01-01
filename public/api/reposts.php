<?php
require_once '../../src/db.php';

session_start();
header("Content-Type: application/json");

// cek login
if (!isset($_SESSION['login']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        "status" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

$pdo = DB::get();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    /* =======================
       GET : Ambil laporan
    ======================== */
    case 'GET':
        $stmt = $pdo->query("
            SELECT r.*, u.name AS user_name, c.name AS category_name
            FROM reports r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN categories c ON r.category_id = c.id
            ORDER BY r.created_at DESC
        ");
        echo json_encode($stmt->fetchAll());
        break;

    /* =======================
       POST : Tambah laporan
    ======================== */
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['title']) ||
            empty($data['description']) ||
            empty($data['category_id'])
        ) {
            http_response_code(400);
            echo json_encode([
                "status" => false,
                "message" => "Data tidak lengkap"
            ]);
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO reports 
            (user_id, category_id, title, description, status) 
            VALUES (?, ?, ?, ?, 'new')
        ");

        $stmt->execute([
            $_SESSION['user_id'],
            $data['category_id'],
            $data['title'],
            $data['description']
        ]);

        echo json_encode([
            "status" => true,
            "message" => "Laporan berhasil ditambahkan"
        ]);
        break;

    /* =======================
       PUT : Update status
    ======================== */
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['id']) || empty($data['status'])) {
            http_response_code(400);
            echo json_encode([
                "status" => false,
                "message" => "ID dan status wajib"
            ]);
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE reports 
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $data['status'],
            $data['id']
        ]);

        echo json_encode([
            "status" => true,
            "message" => "Status laporan diperbarui"
        ]);
        break;

    /* =======================
       DELETE : Hapus laporan
    ======================== */
    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => false,
                "message" => "ID wajib"
            ]);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
        $stmt->execute([$_GET['id']]);

        echo json_encode([
            "status" => true,
            "message" => "Laporan dihapus"
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode([
            "status" => false,
            "message" => "Method tidak diizinkan"
        ]);
}

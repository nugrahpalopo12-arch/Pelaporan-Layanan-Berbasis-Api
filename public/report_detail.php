<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

Auth::check() || header('Location: login.php');

$pdo = DB::get();
$report_id = (int)($_GET['id'] ?? 0);

/* ambil laporan */
$stmt = $pdo->prepare("
  SELECT r.*, u.name AS reporter, c.name AS category
  FROM reports r
  JOIN users u ON u.id = r.user_id
  LEFT JOIN categories c ON c.id = r.category_id
  WHERE r.id = ?
");
$stmt->execute([$report_id]);
$report = $stmt->fetch();

if (!$report) {
  die('Laporan tidak ditemukan');
}

/* ambil komentar */
$stmt = $pdo->prepare("
  SELECT cm.*, u.name, u.role
  FROM comments cm
  JOIN users u ON u.id = cm.user_id
  WHERE cm.report_id = ?
  ORDER BY cm.created_at ASC
");
$stmt->execute([$report_id]);
$comments = $stmt->fetchAll();

/* simpan komentar */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['_csrf'] ?? '')) die('CSRF invalid');

  $text = trim($_POST['comment']);
  if ($text !== '') {
    $stmt = $pdo->prepare("
      INSERT INTO comments (report_id, user_id, comment)
      VALUES (?, ?, ?)
    ");
    $stmt->execute([$report_id, Auth::user()['id'], $text]);

    header("Location: report_detail.php?id=".$report_id);
    exit;
  }
}

$token = csrf_token();
$statusClass = 'status-' . $report['status'];
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Detail Laporan</title>
<link rel="stylesheet" href="./css/comment.css">
</head>
<body>

<a href="report_list.php" class="back-btn">← Kembali</a>

<div class="detail-container">

  <h1 class="detail-title"><?= e($report['title']) ?></h1>

  <div class="detail-meta">
    <div><b>Pelapor:</b> <?= e($report['reporter']) ?></div>
    <div><b>Kategori:</b> <?= e($report['category'] ?? '-') ?></div>
    <div>
      <b>Status:</b>
      <span class="status <?= $statusClass ?>">
        <?= e($report['status']) ?>
      </span>
    </div>
  </div>

  <div class="detail-desc">
    <?= nl2br(e($report['description'])) ?>
  </div>

  <?php if ($report['image_path']): ?>
    <div class="report-image-wrap">
      <img src="<?= e($report['image_path']) ?>" class="report-image" alt="Bukti laporan">
    </div>
  <?php endif; ?>

  <div class="comment-section">
    <h3>Komentar</h3>

    <?php if (!$comments): ?>
      <p class="muted">Belum ada komentar</p>
    <?php endif; ?>

    <div class="comment-list">
      <?php foreach ($comments as $c): ?>
        <div class="comment <?= $c['role']==='admin' ? 'admin' : '' ?>">
          <div class="comment-header">
            <div class="comment-user">
              <?= e($c['name']) ?>
              <?php if ($c['role']==='admin'): ?>
                <span class="comment-role">ADMIN</span>
              <?php endif; ?>
            </div>
            <div class="comment-time">
              <?= e($c['created_at']) ?>
            </div>
          </div>

          <div class="comment-text">
            <?= nl2br(e($c['comment'])) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <form method="post" class="comment-form">
      <input type="hidden" name="_csrf" value="<?= e($token) ?>">
      <textarea name="comment" required placeholder="Tulis komentar..."></textarea>
      <button type="submit">Kirim</button>
    </form>
  </div>

</div>

</body>
</html>

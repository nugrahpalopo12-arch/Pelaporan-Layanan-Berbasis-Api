<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

$pdo = DB::get();

$q = trim($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$per = 10;
$offset = ($page - 1) * $per;

$params = [];
$sqlBase = '
  FROM reports r
  LEFT JOIN users u ON u.id = r.user_id
  LEFT JOIN categories c ON c.id = r.category_id
  WHERE 1=1
';

if ($q !== '') {
  $sqlBase .= ' AND (r.title LIKE ? OR r.description LIKE ? OR u.name LIKE ?)';
  $params[] = "%$q%";
  $params[] = "%$q%";
  $params[] = "%$q%";
}

$totalStmt = $pdo->prepare('SELECT COUNT(*) ' . $sqlBase);
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();

$sql = '
  SELECT r.*, u.name AS reporter, c.name AS category
  ' . $sqlBase . '
  ORDER BY r.created_at DESC
  LIMIT ? OFFSET ?
';

$params[] = $per;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$pages = (int)ceil($total / $per);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Laporan</title>
  <link rel="stylesheet" href="./css/report.css">
</head>
<body>

<a class="back-btn" href="index.php">← Kembali</a>

<form method="get" action="report_list.php">
  <input name="q" value="<?= e($q) ?>" placeholder="Cari laporan...">
  <button type="submit">Search</button>
</form>

<table>
  <thead>
    <tr>
      <th>Judul</th>
      <th>Pelapor</th>
      <th>Kategori</th>
      <th>Status</th>
      <th>Waktu</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>

  <?php if (empty($rows)): ?>
    <tr>
      <td colspan="6">Tidak ada laporan.</td>
    </tr>
  <?php else: foreach ($rows as $r): ?>
    <tr>
      <td><?= e($r['title']) ?></td>
      <td><?= e($r['reporter']) ?></td>
      <td><?= e($r['category']) ?></td>

      <!-- STATUS -->
      <td>
        <?php if (Auth::check() && Auth::user()['role'] === 'admin'): ?>
          <form method="post" action="report_status_update.php" style="margin:0;">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <select name="status" onchange="this.form.submit()">
              <option value="new" <?= $r['status']=='new'?'selected':'' ?>>Baru</option>
              <option value="in_progress" <?= $r['status']=='in_progress'?'selected':'' ?>>Diproses</option>
              <option value="resolved" <?= $r['status']=='resolved'?'selected':'' ?>>Selesai</option>
              <option value="rejected" <?= $r['status']=='rejected'?'selected':'' ?>>Ditolak</option>
            </select>
          </form>
        <?php else: ?>
          <?php
            $status_map = [
              'new' => 'Baru',
              'in_progress' => 'Diproses',
              'resolved' => 'Selesai',
              'rejected' => 'Ditolak'
            ];
            echo e($status_map[$r['status']] ?? $r['status']);
          ?>
        <?php endif; ?>
      </td>

      <td><?= e($r['created_at']) ?></td>

      <!-- AKSI -->
      <td>
        <a class="btn-detail"
           href="report_detail.php?id=<?= (int)$r['id'] ?>">
          Lihat Detail
        </a>
      </td>
    </tr>
  <?php endforeach; endif; ?>

  </tbody>
</table>

<nav class="pagination">
  <?php for ($i = 1; $i <= $pages; $i++): ?>
    <a href="?q=<?= urlencode($q) ?>&page=<?= $i ?>"
       class="<?= $i == $page ? 'active' : '' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>
</nav>

</body>
</html>

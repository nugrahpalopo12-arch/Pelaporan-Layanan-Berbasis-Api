<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

if (!Auth::check()) {
  header('Location: ./login.php'); exit;
}
$pdo = DB::get();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['_csrf'] ?? '')) {
    flash_set('error','Invalid CSRF token');
    header('Location: /report_create.php'); exit;
  }
  $title = trim($_POST['title'] ?? '');
  $desc = trim($_POST['description'] ?? '');
  $cat = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;

  if (!$title || !$desc) {
    flash_set('error','Judul dan deskripsi wajib.');
    header('Location: /report_create.php'); exit;
  }

  $imgPath = null;
  if (!empty($_FILES['image']['name'])) {
    $cfg = require __DIR__ . '/../config/config.php';
    $allowed = ['image/jpeg','image/png'];
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      flash_set('error','Upload error.');
      header('Location: /report_create.php'); exit;
    }
    if ($_FILES['image']['size'] > 2*1024*1024) {
      flash_set('error','File terlalu besar (max 2MB).');
      header('Location: /report_create.php'); exit;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $m = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);
    if (!in_array($m, $allowed)) {
      flash_set('error','Tipe file tidak diizinkan.');
      header('Location: /report_create.php'); exit;
    }
    $ext = $m === 'image/png' ? '.png' : '.jpg';
    $fname = bin2hex(random_bytes(12)) . $ext;
    $cfg = require __DIR__ . '/../config/config.php';
    $dst = rtrim($cfg['upload_dir'], '/\\') . DIRECTORY_SEPARATOR . $fname;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dst)) {
      flash_set('error','Gagal menyimpan file.');
      header('Location: /report_create.php'); exit;
    }
    $imgPath = rtrim($cfg['upload_url'], '/\\') . '/' . $fname;
  }

  $stmt = $pdo->prepare('INSERT INTO reports (user_id, category_id, title, description, image_path, sla_deadline) VALUES (?, ?, ?, ?, ?, ?)');
  $sla = date('Y-m-d H:i:s', strtotime('+3 days'));
  $stmt->execute([ $_SESSION['user']['id'], $cat, $title, $desc, $imgPath, $sla ]);
  flash_set('success','Laporan terkirim.');
  log_action($_SESSION['user']['id'], 'create_report', json_encode(['title'=>$title]));
  header('Location: report_list.php'); exit;
}

$cats = $pdo->query('SELECT * FROM categories')->fetchAll();
$token = csrf_token();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Buat Laporan</title>
<link rel="stylesheet" href="./css/report_create.css">
<body>
<a class="back-btn" href="index.php">← Kembali</a>
<?php if ($m = flash_get('error')): ?><div class="alert error"><?= e($m) ?></div><?php endif; ?>
<form method="post" action="report_create.php" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= e($token) ?>">
  <label>Judul <input type="text" name="title" required></label>
  <label>Kategori
    <select name="category_id">
      <option value="">Pilih</option>
      <?php foreach($cats as $c): ?>
        <option value="<?= e($c['id']) ?>"><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Deskripsi <textarea name="description" required></textarea></label>
  <label>Gambar (opsional) <input type="file" name="image" accept="image/png,image/jpeg"></label>
  <button type="submit">Kirim</button>
</form>
</body></html>

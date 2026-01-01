<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

$pdo = DB::get();
Auth::check();
$user = Auth::user();

$counts = $pdo->query("SELECT status, COUNT(*) as c FROM reports GROUP BY status")->fetchAll();
$summary = ['new'=>0,'in_progress'=>0,'resolved'=>0,'rejected'=>0];
foreach($counts as $r) $summary[$r['status']] = (int)$r['c'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard - Pelaporan</title>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<header>
  <h1>Pelaporan Layanan Publik</h1>
  <nav>
    <span>Halo, <?= e($user['name'] ?? 'Tamu') ?></span>
    <?php if (Auth::check()): ?>
      <a class="btn-logout" href="./logout.php">Logout</a>
    <?php else: ?>
      <a class="btn-outline" href="./login.php">Login</a>
      <a class="btn-primary" href="./register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>

<main>
  <?php if ($m = flash_get('success')): ?><div class="alert success"><?= e($m) ?></div><?php endif; ?>
  <?php if ($m = flash_get('error')): ?><div class="alert error"><?= e($m) ?></div><?php endif; ?>

  <section class="cards">
    <div class="card">Baru<br><strong><?= e($summary['new']) ?></strong></div>
    <div class="card">Sedang Proses<br><strong><?= e($summary['in_progress']) ?></strong></div>
    <div class="card">Selesai<br><strong><?= e($summary['resolved']) ?></strong></div>
    <div class="card">Ditolak<br><strong><?= e($summary['rejected']) ?></strong></div>
  </section>

<section class="chart-card">
  <h2>Grafik Status</h2>
  <canvas id="statusChart" width="400" height="180"></canvas>
</section>

<section class="action-center">
  <a class="btn-primary" href="./report_create.php">Buat Laporan Baru</a>
  <a class="btn-outline" href="./report_list.php">Lihat Daftar Laporan</a>
</section>
</main>

<script>
const data = <?= json_encode(array_values($summary)) ?>;
const labels = ['Baru','Di Proses','Selesai','Ditolak'];
const colors = ['#4e91ff','#ffc107','#28a745','#dc3545'];

const c = document.getElementById('statusChart');
const ctx = c.getContext('2d');
const max = Math.max(...data,1);

ctx.font = "12px Inter, Arial";
ctx.textAlign = "center";

data.forEach((v,i)=>{
  const barW = 60;
  const x = 40 + i * 90;
  const h = (v / max) * 120;

  ctx.fillStyle = colors[i];
  ctx.beginPath();
  ctx.roundRect(x, 150 - h, barW, h, 8);
  ctx.fill();

  ctx.fillStyle = "#333";
  ctx.fillText(labels[i], x + barW / 2, 170);
  ctx.fillText(v, x + barW / 2, 140 - h - 8);
});
</script>
</body>
</html>

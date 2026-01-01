<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['_csrf'] ?? '')) {
    flash_set('error', 'Token CSRF tidak valid.');
    header('Location: login.php'); exit;
  }
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $remember = isset($_POST['remember']);
  if (Auth::login($email, $password, $remember)) {
    flash_set('success', 'Login berhasil.');
    header('Location: index.php'); exit;
  } else {
    flash_set('error', 'Email atau password salah.');
    header('Location: login.php'); exit;
  }
}

$token = csrf_token();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login - Pelaporan</title>
  <link rel="stylesheet" href="./css/form.css"></head>
<body>
<?php if ($m = flash_get('error')): ?>
  <div class="alert error"><?= e($m) ?></div>
<?php endif; ?>
<?php if ($m = flash_get('success')): ?>
  <div class="alert success"><?= e($m) ?></div>
<?php endif; ?>

<form id="loginForm" method="post" action="login.php" novalidate>
  <input type="hidden" name="_csrf" value="<?= e($token) ?>">
  <label>Email
    <input type="email" name="email" required>
  </label>
  <label>Password
    <input type="password" name="password" required minlength="6">
  </label>
  <label><input type="checkbox" name="remember"> Remember me</label>
  <button type="submit">Login</button>
  <p>Belum punya akun? <a href="register.php">Daftar</a></p>
</form>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e){
  const f = e.target;
  if(!f.email.value || !f.password.value){
    e.preventDefault();
    alert('Isi semua field.');
  }
});
</script>
</body>
</html>

<?php 
require_once __DIR__ . '/../src/functions.php'; 
$token = csrf_token(); 
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar Akun - Pelaporan</title>
  <link rel="stylesheet" href="./css/form.css">
  <script>
    function validateForm() {
      const pass = document.getElementById("password").value;
      const confirm = document.getElementById("confirm_password").value;
      if (pass !== confirm) {
        alert("Password tidak cocok!");
        return false;
      }
      return true;
    }
  </script>
</head>

<body>

<?php if ($m = flash_get('error')): ?>
  <div class="alert error"><?= e($m) ?></div>
<?php endif; ?>

<?php if ($m = flash_get('success')): ?>
  <div class="alert success"><?= e($m) ?></div>
<?php endif; ?>

<form method="POST" action="../src/process_register.php" onsubmit="return validateForm();">
  <h2 style="text-align:center; margin-bottom:18px; color:#1e4c87;">Pendaftaran Akun</h2>

  <input type="hidden" name="csrf_token" value="<?= e($token) ?>">

  <label>Nama Lengkap
    <input type="text" name="name" required>
  </label>

  <label>Email
    <input type="email" name="email" required>
  </label>

  <label>Password
    <input type="password" name="password" id="password" required minlength="6">
  </label>

  <label>Konfirmasi Password
    <input type="password" id="confirm_password" required minlength="6">
  </label>

  <button type="submit">Daftar</button>

  <p>Sudah punya akun? <a href="./login.php">Login</a></p>
</form>

</body>
</html>

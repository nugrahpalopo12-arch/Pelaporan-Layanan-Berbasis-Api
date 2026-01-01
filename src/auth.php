<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

class Auth {

  public static function login($email, $password, $remember=false) {
    $pdo = DB::get();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      session_regenerate_id(true);

      $_SESSION['user'] = [
        'id'    => (int)$user['id'],
        'name'  => $user['name'],
        'role'  => $user['role'],
        'email' => $user['email']
      ];

      log_action($user['id'], 'login');

      if ($remember) self::createRememberToken($user['id']);

      return true;
    }
    return false;
  }

  private static function createRememberToken($user_id) {
    $cfg = require __DIR__ . '/../config/config.php';
    $pdo = DB::get();
    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);
    $expires = date('Y-m-d H:i:s', strtotime('+'.$cfg['remember_expire_days'].' days'));

    $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $hash, $expires]);

    setcookie($cfg['remember_cookie_name'], $token, time() + 86400*$cfg['remember_expire_days'], '/', '', isset($_SERVER['HTTPS']), true);
  }

  public static function logout() {
    $cfg = require __DIR__ . '/../config/config.php';

    if (!empty($_COOKIE[$cfg['remember_cookie_name']])) {
      $token = $_COOKIE[$cfg['remember_cookie_name']];
      $hash = hash('sha256', $token);

      $pdo = DB::get();
      $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE token_hash = ?");
      $stmt->execute([$hash]);

      setcookie($cfg['remember_cookie_name'], '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
    }

    session_destroy();
  }

  public static function user() {
    return $_SESSION['user'] ?? null;
  }

  public static function check() {
    return !empty($_SESSION['user']);
  }

  public static function requireRole($role) {
    if (!self::check() || ($_SESSION['user']['role'] !== $role)) {
      header("Location: ../public/login.php");
      exit;
    }
  }
}

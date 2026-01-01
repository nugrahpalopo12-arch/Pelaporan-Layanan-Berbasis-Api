<?php
// src/db.php
class DB {
  private static $pdo = null;
  public static function get() {
    if (self::$pdo === null) {
      $cfg = require __DIR__ . '/../config/config.php';
      $d = $cfg['db'];
      $dsn = "mysql:host={$d['host']};dbname={$d['dbname']};charset={$d['charset']}";
      $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ];
      self::$pdo = new PDO($dsn, $d['user'], $d['pass'], $opt);
    }
    return self::$pdo;
  }
}

$pdo = DB::get();

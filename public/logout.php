<?php
require_once __DIR__ . '/../src/auth.php';
Auth::logout();
header('Location: index.php');
exit;

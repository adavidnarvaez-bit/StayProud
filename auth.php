<?php
declare(strict_types=1);
session_start();

const ADMIN_EMAILS = ["adavidnarvaezm@gmail.com"]; // tu admin real

function json_out(array $data, int $code = 200): void {
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function read_body_json(): array {
  $raw = file_get_contents("php://input");
  $data = json_decode($raw ?: "", true);
  return is_array($data) ? $data : [];
}

function is_admin_email(string $email): bool {
  $email = strtolower(trim($email));
  foreach (ADMIN_EMAILS as $a) {
    if ($email === strtolower(trim($a))) return true;
  }
  return false;
}

function data_path(string $file): string {
  return __DIR__ . "/data/" . $file;
}

function read_json_file(string $path, array $fallback): array {
  if (!file_exists($path)) return $fallback;
  $raw = file_get_contents($path);
  if ($raw === false) return $fallback;
  $data = json_decode($raw, true);
  return is_array($data) ? $data : $fallback;
}

function write_json_file_atomic(string $path, array $data): bool {
  $dir = dirname($path);
  if (!is_dir($dir)) @mkdir($dir, 0775, true);

  $tmp = $path . ".tmp";
  $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  if ($json === false) return false;
  if (file_put_contents($tmp, $json, LOCK_EX) === false) return false;
  return rename($tmp, $path);
}

function require_login(): array {
  if (empty($_SESSION["user_email"])) {
    json_out(["ok"=>false, "error"=>"NO_LOGIN"], 401);
  }
  $email = strtolower(trim((string)$_SESSION["user_email"]));
  return [
    "email" => $email,
    "admin" => is_admin_email($email)
  ];
}
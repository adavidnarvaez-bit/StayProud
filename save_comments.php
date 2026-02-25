<?php
header('Content-Type: application/json; charset=utf-8');

$base = __DIR__ . '/data';
$file = $base . '/comments.json';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
  echo json_encode(["ok" => false, "error" => "JSON inválido"]);
  exit;
}

$email = strtolower(trim($data["email"] ?? ""));
$h = trim($data["review_huesped"] ?? "");
$a = trim($data["review_anfitrion"] ?? "");

if ($email === "") {
  echo json_encode(["ok" => false, "error" => "Falta email"]);
  exit;
}

$db = [];
if (file_exists($file)) {
  $json = file_get_contents($file);
  $tmp = json_decode($json, true);
  if (is_array($tmp)) $db = $tmp;
}

$db[$email] = [
  "email" => $email,
  "review_huesped" => $h,
  "review_anfitrion" => $a,
  "updatedAt" => gmdate("c")
];

$encoded = json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($encoded === false) {
  echo json_encode(["ok" => false, "error" => "No se pudo guardar"]);
  exit;
}

$ok = file_put_contents($file, $encoded, LOCK_EX);
if ($ok === false) {
  echo json_encode(["ok" => false, "error" => "No se pudo escribir comments.json (permisos)"]);
  exit;
}

echo json_encode(["ok" => true]);

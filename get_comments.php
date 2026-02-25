<?php
header('Content-Type: application/json; charset=utf-8');

$base = __DIR__ . '/data';
$file = $base . '/comments.json';

if (!file_exists($file)) {
  echo json_encode([
    "ok" => true,
    "comments" => []
  ]);
  exit;
}

$json = file_get_contents($file);
$data = json_decode($json, true);

if (!is_array($data)) {
  echo json_encode([
    "ok" => true,
    "comments" => []
  ]);
  exit;
}

echo json_encode([
  "ok" => true,
  "comments" => array_values($data)
]);

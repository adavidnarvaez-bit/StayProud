<?php
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/room_data.json';

if (!file_exists($file)) {
  echo json_encode([]);
  exit;
}

$raw = file_get_contents($file);
if (!$raw) {
  echo json_encode([]);
  exit;
}

echo $raw;
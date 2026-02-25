<?php
header('Content-Type: application/json; charset=utf-8');

$uploadDir = __DIR__ . '/uploads/';
$publicDir = 'uploads/';

if (!is_dir($uploadDir)) {
  @mkdir($uploadDir, 0755, true);
}

if (!isset($_FILES['file'])) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'No se recibió archivo']);
  exit;
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Error al subir el archivo']);
  exit;
}

// Límite 8MB (ajusta si quieres)
$maxSize = 8 * 1024 * 1024;
if ($file['size'] > $maxSize) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'El archivo supera 8MB']);
  exit;
}

// Validar tipo (solo imágenes)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowed = ['image/jpeg','image/png','image/webp','image/gif'];
if (!in_array($mime, $allowed, true)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Formato no permitido']);
  exit;
}

// Nombre seguro
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$ext = strtolower($ext ?: 'jpg');

$filename = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$target = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $target)) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'No se pudo guardar en el servidor']);
  exit;
}

// Respuesta con URL
echo json_encode(['ok' => true, 'url' => $publicDir . $filename]);
ç
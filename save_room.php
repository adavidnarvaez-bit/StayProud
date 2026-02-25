<?php
header('Content-Type: application/json; charset=utf-8');

function respond($ok, $payload = []) {
  echo json_encode(array_merge(['ok' => $ok], $payload));
  exit;
}

$raw = file_get_contents('php://input');
if (!$raw) respond(false, ['error' => 'No llegó JSON.']);

$data = json_decode($raw, true);
if (!$data) respond(false, ['error' => 'JSON inválido.']);

$storageFile = __DIR__ . '/room_data.json';

$clean = [
  'descripcion' => isset($data['descripcion']) ? (string)$data['descripcion'] : '',
  'rooms' => isset($data['rooms']) ? (int)$data['rooms'] : 0,
  'beds' => isset($data['beds']) ? (int)$data['beds'] : 0,
  'baths' => isset($data['baths']) ? (int)$data['baths'] : 0,
  'valoracion' => isset($data['valoracion']) ? (int)$data['valoracion'] : 0,

  'servicios' => (isset($data['servicios']) && is_array($data['servicios'])) ? $data['servicios'] : [],
  'comidas' => (isset($data['comidas']) && is_array($data['comidas'])) ? $data['comidas'] : [],
  'acceso' => (isset($data['acceso']) && is_array($data['acceso'])) ? $data['acceso'] : [],
  'bienvenido' => (isset($data['bienvenido']) && is_array($data['bienvenido'])) ? $data['bienvenido'] : [],
  'lavanderia' => (isset($data['lavanderia']) && is_array($data['lavanderia'])) ? $data['lavanderia'] : [],

  'hostPhotoUrl' => isset($data['hostPhotoUrl']) ? (string)$data['hostPhotoUrl'] : '',
  'galleryUrls' => (isset($data['galleryUrls']) && is_array($data['galleryUrls'])) ? array_slice($data['galleryUrls'], 0, 6) : [],

  'updatedAt' => date('c'),
  'updatedBy' => isset($data['updatedBy']) ? (string)$data['updatedBy'] : ''
];

if (file_put_contents($storageFile, json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
  respond(false, ['error' => 'No se pudo guardar room_data.json (permisos).']);
}

respond(true, ['saved' => true]);
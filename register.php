<?php
require_once __DIR__."/auth.php";

$body = read_body_json();

$name = trim((string)($body["name"] ?? ""));
$email = strtolower(trim((string)($body["email"] ?? "")));
$phone = trim((string)($body["phone"] ?? ""));
$city = trim((string)($body["city"] ?? ""));
$password = (string)($body["password"] ?? "");

if ($name === "" || $email === "" || $phone === "" || $city === "") {
  json_out(["ok"=>false, "error"=>"FALTAN_DATOS"], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  json_out(["ok"=>false, "error"=>"EMAIL_INVALIDO"], 400);
}
if (mb_strlen($password) < 8) {
  json_out(["ok"=>false, "error"=>"PASSWORD_MIN_8"], 400);
}

$usersPath = data_path("users.json");
$users = read_json_file($usersPath, []);

foreach ($users as $u) {
  if (strtolower((string)($u["email"] ?? "")) === $email) {
    json_out(["ok"=>false, "error"=>"EMAIL_YA_EXISTE"], 409);
  }
}

$users[] = [
  "id" => bin2hex(random_bytes(8)),
  "name" => $name,
  "email" => $email,
  "phone" => $phone,
  "city" => $city,
  "passwordHash" => password_hash($password, PASSWORD_DEFAULT),
  "createdAt" => gmdate("c"),
  "role" => is_admin_email($email) ? "admin" : "guest"
];

if (!write_json_file_atomic($usersPath, $users)) {
  json_out(["ok"=>false, "error"=>"NO_SE_PUDO_GUARDAR"], 500);
}

// sesión iniciada
$_SESSION["user_email"] = $email;

json_out(["ok"=>true, "admin"=>is_admin_email($email)]);
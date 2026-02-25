<?php
require_once __DIR__."/auth.php";

$body = read_body_json();
$email = strtolower(trim((string)($body["email"] ?? "")));
$password = (string)($body["password"] ?? "");

if ($email === "" || $password === "") {
  json_out(["ok"=>false, "error"=>"FALTAN_CAMPOS"], 400);
}

$users = read_json_file(data_path("users.json"), []);

$found = null;
foreach ($users as $u) {
  if (strtolower((string)($u["email"] ?? "")) === $email) {
    $found = $u;
    break;
  }
}

if (!$found) {
  json_out(["ok"=>false, "error"=>"CREDENCIALES_INVALIDAS"], 401);
}

$hash = (string)($found["passwordHash"] ?? "");
if ($hash === "" || !password_verify($password, $hash)) {
  json_out(["ok"=>false, "error"=>"CREDENCIALES_INVALIDAS"], 401);
}

$_SESSION["user_email"] = $email;

json_out(["ok"=>true, "admin"=>is_admin_email($email)]);

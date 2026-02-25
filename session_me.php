<?php
require_once __DIR__."/auth.php";

if (empty($_SESSION["user_email"])) {
  json_out(["ok"=>true, "logged"=>false]);
}

$email = strtolower(trim((string)$_SESSION["user_email"]));
json_out([
  "ok"=>true,
  "logged"=>true,
  "email"=>$email,
  "admin"=>is_admin_email($email)
]);
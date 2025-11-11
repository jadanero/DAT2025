<?php
include_once '../psw/psw.php';
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    header("HTTP/1.0 401 Unauthorized");
    die();
}

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];
if (in_array($user, array_keys($users)) == false) {
    header("HTTP/1.0 401 Unauthorized");
    die();
}
$hash_esperado = $users[$user];
if (function_exists('hash')) {
    $hash_recibido = hash('sha256', $pass);
} elseif (function_exists('hash_hmac')) {
    $hash_recibido = hash_hmac('sha256', $pass, '');
} else {
    $hash_recibido = sha1($pass);
}

if ($hash_recibido != $hash_esperado) {
    header("HTTP/1.0 401 Unauthorized");
    die();
}

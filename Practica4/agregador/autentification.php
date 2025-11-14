<?php
// Comprobar que la sonda que envia datos o solicita comandos esta autorizada segun psw
include_once '../psw/psw.php';
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    header("HTTP/1.0 401 Unauthorized");
    die();
}

// Obtener credenciales enviadas por la sonda
$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];
if (in_array($user, array_keys($users)) == false) {
    header("HTTP/1.0 401 Unauthorized");
    die();
}

// Comprar la verificacion
$hash_esperado = $users[$user];
if (function_exists('hash')) {
    $hash_recibido = hash('sha256', $pass);
} elseif (function_exists('hash_hmac')) {
    $hash_recibido = hash_hmac('sha256', $pass, '');
} else {
    $hash_recibido = sha1($pass);
}

// Error si no esta verificada
if ($hash_recibido != $hash_esperado) {
    header("HTTP/1.0 401 Unauthorized");
    die();
}

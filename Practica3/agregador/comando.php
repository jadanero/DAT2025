<?php
// Consulta de la sonda para saber si tiene alguna accion pendiente

require_once(__DIR__ . '/autentification.php');
$user = $_SERVER['PHP_AUTH_USER'];

$IDsonda = preg_replace('/[^0-9]/', '', $user); // extrae el número del nombre

$file = __DIR__ . '/accionesPendientes.txt';

// Leer acciones si el fichero existe
if (!file_exists($file)) {
    echo json_encode(["status" => "OK", "acciones" => []]);
    exit;
}

$lineas = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$accionesPendientes = [];

foreach ($lineas as $linea) {
    $accion = json_decode($linea, true);
    if ($accion && isset($accion['IDsonda']) && $accion['IDsonda'] == $IDsonda) {
        $accionesPendientes[] = $accion;
    }
}

// 5Responder
if (empty($accionesPendientes)) {
    echo json_encode(["status" => "OK", "acciones" => []]);
} else {
    echo json_encode(["status" => "OK", "acciones" => $accionesPendientes]);
}
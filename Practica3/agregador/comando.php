<?php
// Consulta de la sonda para saber si tiene alguna acción pendiente

require_once(__DIR__ . '/autentification.php');
$user = $_SERVER['PHP_AUTH_USER'];

// Extrae el número del nombre de usuario (ej: "sonda34" -> 34)
$IDsonda = preg_replace('/[^0-9]/', '', $user);

// Ruta absoluta al fichero de acciones pendientes
$file = '/mnt/usb/www/practica3/agregador/accionesPendientes.txt';

// Si el fichero no existe, devolver lista vacía
if (!file_exists($file)) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "OK", "acciones" => []]);
    exit;
}

// Leer todas las líneas del fichero
$lineas = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$accionesPendientes = [];

// Recorrer cada línea y seleccionar las que correspondan a la sonda actual
foreach ($lineas as $linea) {
    $accion = json_decode($linea, true);
    if ($accion && isset($accion['IDsonda']) && strval($accion['IDsonda']) == strval($IDsonda)) {
        $accionesPendientes[] = $accion;
    }
}

// Responder con las acciones filtradas
header('Content-Type: application/json');
if (empty($accionesPendientes)) {
    echo json_encode(["status" => "OK", "acciones" => []]);
} else {
    echo json_encode(["status" => "OK", "acciones" => $accionesPendientes]);
}


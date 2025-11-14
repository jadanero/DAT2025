<?php
header('Content-Type: application/json');

// Leer el parametro ID de la sonda
$idSonda = isset($_GET['idSonda']) ? trim($_GET['idSonda']) : null;

if ($idSonda === null || $idSonda === '') {
    echo json_encode(["estado" => "error", "mensaje" => "Falta parametro idSonda"]);
    exit;
}

// Construir la ruta al fichero correspondiente
$base = "/mnt/usb/www/practica3/agregador/comando/";
$fichero = $base . "accionesPendientes_" . $idSonda . ".txt";

// Si el fichero no existe o está vacío
if (!file_exists($fichero) || filesize($fichero) === 0) {
    echo json_encode(["estado" => "sin_acciones", "mensaje" => "No hay acciones pendientes"]);
    exit;
}

// Leer las acciones del fichero
$lineas = file($fichero, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$acciones = [];

foreach ($lineas as $linea) {
    $accion = json_decode($linea, true);
    if ($accion !== null) {
        $acciones[] = $accion;
    }
}

// Borrar el fichero o vaciarlo después de entregar las acciones
file_put_contents($fichero, "");

// 6Devolver las acciones en formato JSON
echo json_encode([
    "estado" => "ok",
    "idSonda" => $idSonda,
    "num_acciones" => count($acciones),
    "acciones" => $acciones
], JSON_PRETTY_PRINT);


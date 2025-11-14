<?php
// Recibir una accion ejecutada desde una sonda y guardarla en accionesEjecutadas.txt
header('Content-Type: application/json');

// Leer el JSON recibido
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if ($datos === null) {
    echo json_encode(["estado" => "error", "mensaje" => "JSON no válido"]);
    exit;
}

// Verificar campos requeridos
if (!isset($datos["IDsonda"]) || !isset($datos["accion"]) || !isset($datos["estado"])) {
    echo json_encode(["estado" => "error", "mensaje" => "Faltan campos obligatorios"]);
    exit;
}

$id = $datos["IDsonda"];
$accion = $datos["accion"];
$estado = $datos["estado"];

// Preparar carpeta y fichero de log
$carpeta = "/mnt/usb/www/practica3/agregador/comando/";
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}
$fichero = $carpeta . "accionesEjecutadas.txt";

// Obtener hora del sistema (sin usar date())
$hora = trim(exec('date "+%Y-%m-%d %H:%M:%S"')); // Usamos exec porque con time da error en el router 

// Registrar los datos en formato JSON
$linea = json_encode([
    "fecha" => $hora,
    "IDsonda" => $id,
    "accion" => $accion,
    "estado" => $estado
]);

file_put_contents($fichero, $linea . "\n", FILE_APPEND);

// Respuesta al cliente (la sonda)
echo json_encode([
    "estado" => "ok",
    "mensaje" => "Resultado recibido correctamente",
    "registro" => $linea
]);


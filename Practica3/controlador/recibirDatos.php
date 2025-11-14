<?php
// Script del servidor (webalumnos) que recibe los datos enviados por el router
// en formato JSON mediante POST, los almacena en un fichero local y responde
// con un mensaje de confirmacion.


header('Content-Type: application/json');

// Leer el JSON recibido desde el cuerpo del POST
$json = file_get_contents('php://input');
if (!$json) {
    echo json_encode(["estado" => "error", "mensaje" => "No se recibio ningun dato"]);
    exit;
}

// Decodificar el JSON
$datos = json_decode($json, true);
if ($datos === null) {
    echo json_encode(["estado" => "error", "mensaje" => "Formato JSON no valido"]);
    exit;
}

// Guardar los datos en un fichero local
$fichero = 'datosRecibidos.txt';
$file = fopen($fichero, 'a');
fwrite($file, date('Y-m-d H:i:s') . " " . json_encode($datos) . "\n");
fclose($file);

// 4Responder al router
echo json_encode(["estado" => "ok", "mensaje" => "Datos recibidos correctamente"]);


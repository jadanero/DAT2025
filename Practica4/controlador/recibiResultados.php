<?php
// Recibe los resultados de acciones ejecutadas por las sondas (vía router).
// Guarda cada JSON recibido en resultadosRecibidos.txt


header('Content-Type: application/json');

// Leer JSON recibido
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

// Verificar formato
if ($datos === null || !is_array($datos)) {
    echo json_encode(["estado" => "error", "mensaje" => "JSON no valido"]);
    exit;
}

// Fichero destino
$fichero = 'resultadosRecibidos.txt';
if (!file_exists($fichero)) {
    file_put_contents($fichero, '');
}

// Guardar resultados (uno por linea)
foreach ($datos as $linea) {
    file_put_contents($fichero, json_encode($linea) . "\n", FILE_APPEND);
}

// Confirmar
echo json_encode([
    "estado" => "ok",
    "mensaje" => "Resultados recibidos correctamente",
    "num_registros" => count($datos)
]);



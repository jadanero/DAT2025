<?php

// Devuelve las acciones pendientes en formato JSON ya estructurado.
// Cada linea del fichero tiene el formato: ID:{"IDsonda":"..","accion":"..","param":".."}


header('Content-Type: application/json');

// ver la existencia del fichero de acciones pendientes y el contenido
$fichero = 'accionesPendientes.txt';
if (!file_exists($fichero)) {
    file_put_contents($fichero, '');
}

// recoger la id de la sonda y la accion
$idSonda = isset($_GET['idSonda']) ? trim($_GET['idSonda']) : null;
$contenido = trim(file_get_contents($fichero));


if ($contenido === '') {
    echo json_encode(["estado" => "sin_acciones", "mensaje" => "No hay acciones pendientes"]);
    exit;
}

// ver la accion de la sonda que lo ha pedido y luego borrarla del txt
$lineas = explode("\n", $contenido);
$accionesSonda = [];
$otrasAcciones = [];

foreach ($lineas as $linea) {
    $linea = trim($linea);
    if ($linea === '') continue;

    // Dividir en "ID" y JSON
    list($id, $json) = explode(":", $linea, 2);

    if ($idSonda === null || $idSonda === $id) {
        $accionesSonda[] = json_decode($json, true); // anadir el objeto JSON
    } else {
        $otrasAcciones[] = $linea; // mantener el resto
    }
}

// Responder al router
if (empty($accionesSonda)) {
    echo json_encode(["estado" => "sin_acciones", "mensaje" => "No hay acciones para esta sonda"]);
    exit;
}

echo json_encode([
    "estado" => "ok",
    "idSonda" => $idSonda,
    "num_acciones" => count($accionesSonda),
    "acciones" => $accionesSonda
], JSON_PRETTY_PRINT);

// Guardar las acciones restantes
file_put_contents($fichero, implode("\n", $otrasAcciones) . "\n");
?>


<?php
// Mandar al servidor todos los resultado de acciones ejecutada guardadas en el txt
header('Content-Type: text/plain');

// Ruta local del fichero con resultados
$ficheroResultados = "/mnt/usb/www/practica3/agregador/comando/accionesEjecutadas.txt";

// URL del servidor que los recibira
$urlServidor = "https://webalumnos.tlm.unavarra.es:10401/controlador/recibirResultados.php";

// Comprobar si hay algo que enviar
if (!file_exists($ficheroResultados) || filesize($ficheroResultados) === 0) {
    echo "[" . exec('date "+%Y-%m-%d %H:%M:%S"') . "] No hay resultados para enviar\n";
    exit;
}

// Leer el contenido (varias líneas JSON)
$lineas = file($ficheroResultados, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$datos = [];

foreach ($lineas as $linea) {
    $json = json_decode($linea, true);
    if ($json !== null) {
        $datos[] = $json;
    }
}

// Crear contexto HTTPS
$opciones = [
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/json\r\n",
        "content" => json_encode($datos),
        "ignore_errors" => true
    ]
];
$contexto = stream_context_create($opciones);

// Enviar al servidor
$respuesta = @file_get_contents($urlServidor, false, $contexto);

if ($respuesta === false) {
    echo "[" . exec('date "+%Y-%m-%d %H:%M:%S"') . "]  Error al enviar resultados al servidor\n";
    exit;
}

echo "[" . exec('date "+%Y-%m-%d %H:%M:%S"') . "]  Respuesta del servidor: $respuesta\n";

// Limpiar el fichero local despues de enviar
file_put_contents($ficheroResultados, "");

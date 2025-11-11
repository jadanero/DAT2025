<?php
// La sonda hace un post autenticado. El agregador:
// 1- Verifica credenciales
// 2- Guarda resultado en el fichero
// 3- Elimina la accion del fichero

// Autenticación
require_once(__DIR__ . '/autentification.php');
$user = $_SERVER['PHP_AUTH_USER']; // si llega aquí, ya está autenticado

// Leer cuerpo JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['IDsonda']) || !isset($data['accion']) || !isset($data['resultado'])) {
    echo json_encode(["status" => "ERROR", "msg" => "Datos incompletos"]);
    exit;
}

// Guardar en log de acciones ejecutadas
$fileLog = '/mnt/usb/www/practica3/accionesEjecutadas.txt';
$linea = json_encode([
    "timestamp" => date("Y-m-d H:i:s"),
    "IDsonda"   => $data['IDsonda'],
    "accion"    => $data['accion'],
    "resultado" => $data['resultado']
]);
file_put_contents($fileLog, $linea . "\n", FILE_APPEND);

// Borrar la acción correspondiente de las pendientes
$filePend = '/mnt/usb/www/practica3/accionesPendientes.txt';
if (file_exists($filePend)) {
    $lineas = file($filePend, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $nuevas = [];

    foreach ($lineas as $l) {
        $a = json_decode($l, true);
        if ($a) {
            // mantener las que NO coincidan con la acción ejecutada
            if (!($a['IDsonda'] == $data['IDsonda'] && $a['accion'] == $data['accion'])) {
                $nuevas[] = json_encode($a);
            }
        }
    }

    file_put_contents($filePend, implode("\n", $nuevas));
}

echo json_encode(["status" => "OK", "msg" => "Acción registrada y actualizada"]);

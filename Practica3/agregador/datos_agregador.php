<?php
// datos_controlador.php
// Reenvía los datos de la carpeta 'datos/' al controlador y luego los borra

$carpeta = __DIR__ . "/datos";  // Carpeta donde se guardan los datos
//$url_controlador = "http://localhost:6000/controlador_simulado.php";  // Dirección del controlador

// Comprobar que la carpeta existe
if (!is_dir($carpeta)) {
    echo "No existe la carpeta de datos ($carpeta)\n";
    exit();
}

// Buscar archivos .json dentro de /datos
$archivos = glob($carpeta . "/*.json");
if (count($archivos) == 0) {
    echo "No hay datos que reenviar.\n";
    exit();
}

foreach ($archivos as $archivo) {
    // Leer el contenido JSON del archivo
    $contenido = file_get_contents($archivo);
    $data = json_decode($contenido, true);

    if (!$data) {
        echo "Archivo inválido.\n";
        continue;
    }

    // Enviar al controlador con curl -----------------------------------------
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $respuesta = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        echo "Enviado correctamente: " . basename($archivo) . "\n";
        echo "Respuesta: $respuesta\n";
        unlink($archivo);  // Borrar archivo tras envío correcto
    } else {
        echo "Error al enviar " . basename($archivo) . " (HTTP $http_code)\n";
    }
}

echo "Proceso de reenvío completado.\n";
?>
